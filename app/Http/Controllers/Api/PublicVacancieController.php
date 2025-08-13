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
                    $file     = $request->file("attachment");
                    $stored   = $file->store("recruitments/attachments");
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
                        
                        ETAPA 1 — EXTRAÇÃO
                        - Extraia TODO o conteúdo textual do arquivo ANEXADO (PDF/DOC/DOCX/Imagem), cobrindo todas as páginas.
                        - Ignore cabeçalhos/rodapés repetidos, números de página e artefatos.
                        - Não traduza e não reescreva: preserve a grafia original.
                        
                        ETAPA 2 — ESTRUTURAÇÃO (SAÍDA EXCLUSIVA EM JSON)
                        - A partir do texto extraído, identifique as informações e RETORNE **APENAS** um JSON **válido** (UTF-8), sem comentários, sem markdown e sem qualquer texto extra.
                        - O JSON deve seguir **exatamente** a estrutura e **ordem** de chaves abaixo. Não crie chaves novas.
                        - Se um dado não for encontrado com segurança, use `null` (ou `{}` / `[]` conforme o tipo). Não invente.
                        
                        ESTRUTURA (mantenha exatamente as chaves e a ordem):
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
                                // Para cada formação identificada:
                                // Use como CHAVE o nome do curso conforme aparece no currículo
                                // e como VALOR um objeto: { "Instituição": <string|null> }.
                                // Se não houver formações, mantenha {}.
                            },
                            "Experiência Profissional": {
                                // Para cada experiência:
                                // Use como CHAVE o nome da empresa conforme aparece no currículo.
                                // Valor: { "Cargo": <string|null>, "Período": <string|null> }.
                                // Se houver múltiplos cargos na mesma empresa, crie chaves adicionais com sufixo " (2)", " (3)", etc.
                                // Se não houver experiências, mantenha {}.
                            },
                            "Cursos Complementares": {
                                // Liste cursos/treinamentos curtos:
                                // Cada curso é uma CHAVE (ex.: "Mecânico Geral – SENAI") com valor {}.
                                // Se não houver, mantenha {}.
                            },
                            "additional_info": <string|null>,
                            "career_summary": <string|null>
                        }
                        
                        REGRAS ESPECÍFICAS
                        - **Português em todas as chaves/títulos** (exatamente como definidos acima).
                        - **Não inclua** informações de contato, endereço ou data de nascimento dentro de "additional_info".
                        - Tudo que **não** se encaixar claramente nas seções definidas deve ir para "additional_info" (ex.: prêmios, voluntariado, links de portfólio, disponibilidade, CNH, veículos, pretensão, observações diversas).
                        - "Resumo Profissional" → "Qualificações": bullets **concretos** e extraídos do texto (experiências, resultados, ferramentas). Se não houver, use `[]`.
                        - "career_summary": produzir **no fim**, curto, objetivo e **imparcial**, apenas com o que estiver no currículo (sem elogios genéricos). Se não houver base suficiente, use `null`.
                        - Datas/períodos: preserve como no texto. Se precisar normalizar, use meses pt-BR abreviados: "Jan.", "Fev.", "Mar.", "Abr.", "Mai.", "Jun.", "Jul.", "Ago.", "Set.", "Out.", "Nov.", "Dez.".
                        - Em casos parciais (ex.: empresa sem cargo), preencha o que existir e o restante com `null`.
                        - Não crie campos fora da estrutura. Não repita dados em múltiplas seções.
                        
                        VALIDAÇÃO FINAL
                        - Retorne **apenas** o JSON válido exatamente nesse formato e ordem de chaves.
                        - Sem backticks, sem explicações, sem texto adicional.
                        PROMPT;
                        

                        $resp = Http::withToken(env("OPENAI_API_KEY"))
                            ->acceptJson()
                            ->asJson()
                            ->post("https://api.openai.com/v1/responses", [
                                "model" => "gpt-3.5-turbo",
                                "input" => [
                                    [
                                        "role"    => "user",
                                        "content" => [
                                            ["type" => "input_text", "text" => $prompt],
                                            ["type" => "input_file", "file_id" => $openaiFileId],
                                        ],
                                    ],
                                ],
                                "temperature" => 0.2,
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

