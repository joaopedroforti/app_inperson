<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use App\Models\CalculationResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Vacancy;
use App\Models\Recruitment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;

class PublicVacancieController extends BaseApiController
{

    public function candidate(Request $request)
    {
        $company = $request->get("company");
        if (!$this->checkAccess($company, "recruitment", "post")) {
            return response()->json(["error" => "Acesso negado à API de Calculos (POST)"], 403);
        }

        // se debug=1, após a chamada à OpenAI retornaremos SOMENTE o JSON bruto dela
        $debug = method_exists($request, "boolean") ? $request->boolean("debug", false) : (bool)$request->input("debug", false);

        // 1) Validação
        $data = $request->validate([
            "vacancy_reference" => "required|string",
            "full_name"         => "required|string",
            "document"          => "required|string",
            "nascimento"        => "nullable|date",
            "mail"              => "nullable|email",
            "phone"             => "nullable|string",
            "cep"               => "nullable|string",
            "number"            => "nullable|string",
            "complemento"       => "nullable|string",
            "linkedin"          => "nullable|string",
            "questions"         => "nullable",  // array ou string
            "attachment"        => "nullable|file|max:15360",
            "debug"             => "nullable",
        ]);

        // 2) Normalizações
        $idCompany = data_get($company, "id_company") ?? data_get($company, "id") ?? session("company_id");
        $cpf       = preg_replace("/\\D+/", "", (string) $data["document"]);
        $birth     = !empty($data["nascimento"]) ? date("Y-m-d", strtotime($data["nascimento"])) : null;

        $questionsRaw  = $data["questions"] ?? null;
        $questionsJson = is_array($questionsRaw) ? json_encode($questionsRaw, JSON_UNESCAPED_UNICODE)
                                                 : (is_string($questionsRaw) ? $questionsRaw : null);

        // 3) Vaga por referência
        $vacancy = Vacancy::where("reference", $data["vacancy_reference"])->first();
        if (!$vacancy) return response()->json(["error" => "Vaga não encontrada para a referência informada."], 404);
        $vacancyId = $vacancy->id_vacancie ?? $vacancy->id_vacancy ?? $vacancy->id;

        // 4) Cria/recupera Person + Recruitment (upload e extração via OpenAI com arquivo cru)
        $savedFileMeta = null;
        $curriculum    = null; // <- substitui extractedText

        // variáveis para retorno imediato quando debug=1
        $openaiImmediateResponse = null;
        $openaiImmediateStatus   = null;

        // Variáveis para armazenar os resultados da transação
        $person = null;

        try {
            DB::transaction(function () use (
                $idCompany, $cpf, $birth, $data, $questionsJson, $vacancyId,
                &$savedFileMeta, &$curriculum,
                &$openaiImmediateResponse, &$openaiImmediateStatus,
                $request, $debug, &$person
            ) {
                // Pessoa (upsert sem sobrescrever com vazio)
                $person = Person::where("id_company", $idCompany)->where("cpf", $cpf)->first();
                if (!$person) {
                    $person = Person::create([
                        "id_company"         => $idCompany,
                        "full_name"          => $data["full_name"],
                        "cpf"                => $cpf,
                        "birth_date"         => $birth,
                        "personal_email"     => $data["mail"] ?? null,
                        "phone"              => $data["phone"] ?? null,
                        "zip_code"           => $data["cep"] ?? null,
                        "address_number"     => $data["number"] ?? null,
                        "address_complement" => $data["complemento"] ?? null,
                        "status"             => "active",
                    ]);
                } else {
                    $updates = array_filter([
                        "full_name"          => $data["full_name"] ?? null,
                        "birth_date"         => $birth,
                        "personal_email"     => $data["mail"] ?? null,
                        "phone"              => $data["phone"] ?? null,
                        "zip_code"           => $data["cep"] ?? null,
                        "address_number"     => $data["number"] ?? null,
                        "address_complement" => $data["complemento"] ?? null,
                    ], fn($v) => !is_null($v) && trim((string)$v) !== "");
                    if (!empty($updates)) $person->update($updates);
                }

                // Upload + extração via OpenAI (arquivo cru)
                if ($request->hasFile("attachment")) {
                    /** @var UploadedFile $file */
                    $file = $request->file("attachment");
                
                    // Força a extensão para .pdf
                    $newName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.pdf';
                
                    // Salva já com o nome novo
                    $stored = $file->storeAs("recruitments/attachments", $newName);
                
                   
                    /** @var UploadedFile $file */
                    $file     = $request->file("attachment");
                    $fullPath = Storage::path($stored);
                    $mime     = strtolower((string) $file->getMimeType());

                    // 4.1) Files API — envia o arquivo cru
                    $fileUpload = Http::withToken(env("OPENAI_API_KEY"))
                        ->asMultipart()
                        ->attach("file", fopen($fullPath, "r"), $file->getClientOriginalName())
                        ->attach("purpose", "assistants")
                        ->post("https://api.openai.com/v1/files");

                    if ($fileUpload->successful()) {
                        $openaiFileId = data_get($fileUpload->json(), "id");

                        // 4.2) Responses API — referência ao arquivo DENTRO de "input"
                       
                        $prompt = <<<PROMPT
Você é um especialista em extração de informações de currículos.

OBJETIVO:
Ler o arquivo ANEXADO (PDF, DOC, DOCX ou imagem) e extrair todo o texto, organizando-o em um JSON **válido** (UTF-8) seguindo exatamente a estrutura abaixo.  
Não adicionar texto, comentários, markdown ou quebras de linha extras fora do JSON.  
A saída deve ser **apenas** o JSON puro, sem ```json ou qualquer outra marcação.

ETAPA 1 — EXTRAÇÃO
- Extraia todo o texto do arquivo, incluindo todas as páginas.
- Ignore cabeçalhos/rodapés repetidos, números de página e artefatos visuais.
- Preserve a grafia original. Não traduza e não reescreva.

ETAPA 2 — ESTRUTURAÇÃO
- Preencher exatamente as chaves e a ordem especificada abaixo.
- Se o dado não existir ou não puder ser identificado com segurança, usar `null`, `{}` ou `[]` conforme o tipo esperado.
- Não inventar ou duplicar dados.
- Não criar chaves extras ou alterar nomes das chaves.

ESTRUTURA:
{
    "Informações Pessoais": {
        "Nome": <string|null>,
        "Nacionalidade": <string|null>,
        "Estado Civil": <string|null>,
        "Data de Nascimento": <string|null>,
        "Endereço": <string|null>,
        "Contatos": {
            "Telefone": <string|null>,
            "E-mail": <string|null>
        }
    },
    "Objetivo": {
        "Área de Atuação": <string|null>
    },
    "Resumo Profissional": {
        "Qualificações": [
            <string>, ...
        ]
    },
    "Formação": {
        // CHAVE = Nome do curso conforme no currículo
        // VALOR = { "Instituição": <string|null> }
    },
    "Experiência Profissional": {
        // CHAVE = Nome da empresa conforme no currículo
        // VALOR = { "Cargo": <string|null>, "Período": <string|null> }
        // Se múltiplos cargos na mesma empresa, usar sufixo (2), (3)...
    },
    "Cursos Complementares": {
        // CHAVE = Nome do curso
        // VALOR = {}
    },
    "Informações Adicionais": <string|null>,
    "Resumo": <string|null>
}

REGRAS ESPECÍFICAS:
- Português em todas as chaves, exatamente como no modelo.
- Não colocar contatos, endereço ou data de nascimento em "Informações Adicionais".
- Tudo que não se encaixar claramente nas seções definidas vai para "Informações Adicionais".
- "Resumo Profissional" → "Qualificações": lista de habilidades, resultados ou ferramentas concretas extraídas do texto. Se não houver, usar `[]`.
- "Resumo": frase curta e objetiva no final, apenas com base no currículo (sem adjetivos genéricos). Se não houver base suficiente, `null`.
- Datas/períodos: manter como no texto. Se normalizar, usar meses abreviados em pt-BR ("Jan.", "Fev.", ...).
- Em casos parciais, preencher apenas o que existir e usar `null` para o restante.

VALIDAÇÃO FINAL:
- A saída deve ser **somente** o JSON puro, válido e bem formatado.
- Sem markdown, sem comentários e sem qualquer texto antes ou depois.
PROMPT;


                        $resp = Http::withToken(env("OPENAI_API_KEY"))
                            ->acceptJson()
                            ->asJson()
                            ->post("https://api.openai.com/v1/responses", [
                                "model" => "gpt-5-nano",
                                "input" => [
                                    [
                                        "role"    => "user",
                                        "content" => [
                                            ["type" => "input_text", "text" => $prompt],
                                            ["type" => "input_file", "file_id" => $openaiFileId],
                                        ],
                                    ],
                                ],

                            ]);

                        // captura para retorno imediato no modo debug
                        $openaiImmediateResponse = $resp->json();
                        $openaiImmediateStatus   = $resp->status();

                        // se NÃO estiver em debug, seguimos extraindo texto normalmente
                        if ($resp->successful() && !$debug) {
                            $curriculum = $this->parseOpenAIResponseText($openaiImmediateResponse);
                            if (is_string($curriculum)) {
                                $curriculum = preg_replace("/\\s+/", " ", trim($curriculum));
                            }
                        }
                    }

                    // Metadados do arquivo
                    $savedFileMeta = [
                        "path"     => $stored,
                        "mime"     => $mime,
                        "size"     => $file->getSize(),
                        "filename" => $file->getClientOriginalName(),
                    ];
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "Falha ao criar pessoa/recruitment.",
                "trace" => app()->isProduction() ? null : $e->getMessage(),
            ], 500);
        }

        // ===== retorno imediato SOMENTE com a resposta da OpenAI quando debug=1 =====
        if ($debug) {
            if (!is_null($openaiImmediateResponse)) {
                return response()->json($openaiImmediateResponse, $openaiImmediateStatus ?? 200);
            }
            return response()->json([
                "error" => "Debug=1 enviado, mas não houve resposta da OpenAI. Envie um arquivo em \"attachment\" (multipart/form-data)."
            ], 400);
        }

        // Obter IDs necessários para o retorno
        $personId = $person->id_person ?? $person->id;

        // Criação ou atualização do Recruitment fora da transação para otimização
        $recruitment = Recruitment::create([
            "id_company" => $idCompany,
            "id_person"  => $personId,
            "id_vacancy" => $vacancyId,
            "questions"  => $questionsJson,
            "curriculum" => $curriculum ?? null,
        ]);
        $recruitmentId = $recruitment->id_recruitment ?? $recruitment->id;
        
        // Dados fictícios para completar o retorno (você deve implementar a lógica real)
        $profileName = "Perfil Candidato"; // Implementar lógica real
        $attributes = []; // Implementar lógica real
        $skillsNorm = []; // Implementar lógica real

        return response()->json([
            "id_person"      => $personId,
            "id_recruitment" => $recruitmentId,
            "id_vacancy"     => $vacancyId,
            "curriculum"     => $curriculum, // <- novo nome no retorno
        ], 201);

    }

    /**
     * Extrai texto das diferentes formas possíveis da Responses API.
     * Prioridade:
     * 1) output_text
     * 2) output[].content[].text|value
     * 3) choices[0].message.content (compat)
     */
    private function parseOpenAIResponseText(?array $json): ?string
    {
        if (!$json) return null;

        $direct = data_get($json, "output_text");
        if (is_string($direct) && trim($direct) !== "") return $direct;

        $output = data_get($json, "output", []);
        if (is_array($output)) {
            foreach ($output as $block) {
                $contents = data_get($block, "content", []);
                if (is_array($contents)) {
                    foreach ($contents as $c) {
                        $t = data_get($c, "text") ?? data_get($c, "value") ?? null;
                        if (is_string($t) && trim($t) !== "") return $t;
                    }
                }
            }
        }

        $choices = data_get($json, "choices", []);
        if (is_array($choices) && isset($choices[0])) {
            $msg = data_get($choices[0], "message.content");
            if (is_string($msg) && trim($msg) !== "") return $msg;
        }

        return null;
    }
}


