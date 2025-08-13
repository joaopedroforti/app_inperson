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
    public function calculation(Request $request)
    {
        $base = 'https://api1.inperson.com.br';
        $timeout = 10;

        try {
            $skillsResp = Http::acceptJson()->timeout($timeout)->get("{$base}/profiles/skills");
            $adjResp    = Http::acceptJson()->timeout($timeout)->get("{$base}/profiles/adjectives");

            if ($skillsResp->failed() || $adjResp->failed()) {
                return response()->json([
                    'message' => 'Falha ao consultar serviços externos.',
                    'skills_status' => $skillsResp->status(),
                    'adjectives_status' => $adjResp->status(),
                ], 502);
            }

            $skills = collect(data_get($skillsResp->json(), 'data', []))
                ->map(fn ($it) => ['id' => data_get($it, 'id'), 'description' => data_get($it, 'description')])
                ->values();

            $adjectives = collect(data_get($adjResp->json(), 'data', []))
                ->map(fn ($it) => ['id' => data_get($it, 'id'), 'description' => data_get($it, 'description')])
                ->values();

            return response()->json(['skills' => $skills, 'adjectives' => $adjectives]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro inesperado ao obter dados.',
                'error'   => app()->isProduction() ? null : $e->getMessage(),
            ], 500);
        }
    }

    public function candidate(Request $request)
    {
        $company = $request->get('company');
        if (!$this->checkAccess($company, 'recruitment', 'post')) {
            return response()->json(['error' => 'Acesso negado à API de Calculos (POST)'], 403);
        }

        // se debug=1, após a chamada à OpenAI retornaremos SOMENTE o JSON bruto dela
        $debug = method_exists($request, 'boolean') ? $request->boolean('debug', false) : (bool)$request->input('debug', false);

        // 1) Validação
        $data = $request->validate([
            'vacancy_reference' => 'required|string',
            'full_name'         => 'required|string',
            'document'          => 'required|string',
            'nascimento'        => 'nullable|date',
            'mail'              => 'nullable|email',
            'phone'             => 'nullable|string',
            'cep'               => 'nullable|string',
            'number'            => 'nullable|string',
            'complemento'       => 'nullable|string',
            'linkedin'          => 'nullable|string',
            'questions'         => 'nullable',  // array ou string

            // cálculo externo
            'adjectives'        => 'required|array|min:1',
            'adjectives.*'      => 'integer',
            'skills'            => 'required|array|min:1',
            'skills.*.id'       => 'required|integer',
            'skills.*.points'   => 'required|numeric',

            // arquivo cru (qualquer tipo) — 15MB
            'attachment'        => 'nullable|file|max:15360',
            'debug'             => 'nullable',
        ]);

        // 2) Normalizações
        $idCompany = data_get($company, 'id_company') ?? data_get($company, 'id') ?? session('company_id');
        $cpf       = preg_replace('/\D+/', '', (string) $data['document']);
        $birth     = !empty($data['nascimento']) ? date('Y-m-d', strtotime($data['nascimento'])) : null;

        $questionsRaw  = $data['questions'] ?? null;
        $questionsJson = is_array($questionsRaw) ? json_encode($questionsRaw, JSON_UNESCAPED_UNICODE)
                                                 : (is_string($questionsRaw) ? $questionsRaw : null);

        // 3) Vaga por referência
        $vacancy = Vacancy::where('reference', $data['vacancy_reference'])->first();
        if (!$vacancy) return response()->json(['error' => 'Vaga não encontrada para a referência informada.'], 404);
        $vacancyId = $vacancy->id_vacancie ?? $vacancy->id_vacancy ?? $vacancy->id;

        // 4) Cria/recupera Person + Recruitment (upload e extração via OpenAI com arquivo cru)
        $savedFileMeta = null;
        $curriculum    = null; // <- substitui extractedText

        // variáveis para retorno imediato quando debug=1
        $openaiImmediateResponse = null;
        $openaiImmediateStatus   = null;

        try {
            $result = DB::transaction(function () use (
                $idCompany, $cpf, $birth, $data, $questionsJson, $vacancyId,
                &$savedFileMeta, &$curriculum,
                &$openaiImmediateResponse, &$openaiImmediateStatus,
                $request, $debug
            ) {
                // Pessoa (upsert sem sobrescrever com vazio)
                $person = Person::where('id_company', $idCompany)->where('cpf', $cpf)->first();
                if (!$person) {
                    $person = Person::create([
                        'id_company'         => $idCompany,
                        'full_name'          => $data['full_name'],
                        'cpf'                => $cpf,
                        'birth_date'         => $birth,
                        'personal_email'     => $data['mail'] ?? null,
                        'phone'              => $data['phone'] ?? null,
                        'zip_code'           => $data['cep'] ?? null,
                        'address_number'     => $data['number'] ?? null,
                        'address_complement' => $data['complemento'] ?? null,
                        'status'             => 'active',
                    ]);
                } else {
                    $updates = array_filter([
                        'full_name'          => $data['full_name'] ?? null,
                        'birth_date'         => $birth,
                        'personal_email'     => $data['mail'] ?? null,
                        'phone'              => $data['phone'] ?? null,
                        'zip_code'           => $data['cep'] ?? null,
                        'address_number'     => $data['number'] ?? null,
                        'address_complement' => $data['complemento'] ?? null,
                    ], fn($v) => !is_null($v) && trim((string)$v) !== '');
                    if (!empty($updates)) $person->update($updates);
                }

                // Recruitment
                $recruitment = Recruitment::create([
                    'id_company' => $idCompany,
                    'id_person'  => $person->id_person ?? $person->id,
                    'id_vacancy' => $vacancyId,
                    'questions'  => $questionsJson,
                ]);

                // Upload + extração via OpenAI (arquivo cru)
                if ($request->hasFile('attachment')) {
                    /** @var UploadedFile $file */
                    $file     = $request->file('attachment');
                    $stored   = $file->store('recruitments/attachments');
                    $fullPath = Storage::path($stored);
                    $mime     = strtolower((string) $file->getMimeType());

                    // 4.1) Files API — envia o arquivo cru
                    $fileUpload = Http::withToken(env('OPENAI_API_KEY'))
                        ->asMultipart()
                        ->attach('file', fopen($fullPath, 'r'), $file->getClientOriginalName())
                        ->attach('purpose', 'assistants')
                        ->post('https://api.openai.com/v1/files');

                    if ($fileUpload->successful()) {
                        $openaiFileId = data_get($fileUpload->json(), 'id');

                        // 4.2) Responses API — referência ao arquivo DENTRO de "input"
                        $prompt = <<<PROMPT
Você é um especialista em extração de currículos. 
Tarefa em 2 etapas obrigatórias:

ETAPA 1 — EXTRAÇÃO:
- Extraia TODO o conteúdo textual do arquivo ANEXADO (PDF/DOC/DOCX/Imagem). 
- Inclua texto de todas as páginas. Ignore cabeçalhos/rodapés repetidos, números de página e artefatos de conversão.
- Não traduza, não reescreva: preserve a grafia original (acentos, maiúsculas/minúsculas).

ETAPA 2 — ESTRUTURAÇÃO (SAÍDA EXCLUSIVA EM JSON):
- A partir do texto extraído, identifique informações e RETORNE APENAS um JSON, sem comentários, sem markdown, sem texto extra.
- O JSON DEVE TER EXATAMENTE a estrutura e a ordem de chaves abaixo. TODAS as chaves devem existir. 
- Se um dado não for encontrado com segurança no texto, preencha com null (ou objeto vazio {} onde aplicável). NÃO invente.

ESTRUTURA E REGRAS (mantenha exatamente as chaves e a ordem):

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
            <string>, ... // lista de bullets resumindo qualificações/competências concretas encontradas no texto; se não houver, use []
        ]
    },
    "Formação": {
        // Para cada formação identificável:
        // Use como CHAVE o nome do curso conforme aparece no currículo (ex.: "Ensino Médio Completo – Técnico de Mecânica")
        // O VALOR deve ser um objeto com "Instituição": <string|null>
        // Exemplo:
        // "Administração de Empresas (Bacharelado)": { "Instituição": "USP" }
        // Se não houver formações, deixe como {}
    },
    "Experiência Profissional": {
        // Para cada experiência:
        // Use como CHAVE o nome da empresa conforme aparece no currículo.
        // O VALOR deve ser: { "Cargo": <string|null>, "Período": <string|null> }
        // Formato do período: preservar como no texto; se precisar normalizar, use "Mês abreviado. AAAA a Mês abreviado. AAAA" (ex.: "Fev. 2014 a Mar. 2020") ou "AAAA a AAAA".
        // Se houver múltiplos cargos para a MESMA empresa, crie chaves adicionais com sufixo " (2)", " (3)", etc.
        // Se não houver experiências, deixe como {}
    },
    "Cursos Complementares": {
        // Liste cursos extras/treinamentos/certificações curtas.
        // Cada curso deve ser uma CHAVE (ex.: "Mecânico Geral – SENAI"), com valor {}.
        // Se não houver, deixe como {}
    }
}

REGRAS ADICIONAIS E DE DESAMBIGUAÇÃO:
- Não inclua informações de contato, endereços ou datas de nascimento fora do bloco "Informações Pessoais".
- Não crie campos fora da estrutura exigida.
- Não inclua "additional_info" ou quaisquer chaves diferentes das especificadas.
- Datas e períodos: mantenha o texto original quando disponível; ao normalizar meses, use abreviações pt-BR como "Jan.", "Fev.", "Mar.", "Abr.", "Mai.", "Jun.", "Jul.", "Ago.", "Set.", "Out.", "Nov.", "Dez.".
- Telefones: mantenha o formato apresentado no currículo quando possível.
- "Resumo Profissional" → "Qualificações": crie bullets concisos SÓ com base no que realmente consta no texto (experiências, habilidades, resultados, ferramentas). Evite frases vagas; não invente.
- Se existir apenas parte da informação (ex.: empresa sem cargo), preencha o que existir e o restante com null.
- Se o documento contiver tabelas/listas, leia-as integralmente.
- Caso alguma seção seja explicitamente inexistente, retorne o objeto/lista exigido(a) vazio(a) conforme instruções acima.

VALIDAÇÃO DA SAÍDA:
- Retorne APENAS JSON válido UTF-8.
- Sem comentários, sem backticks, sem texto antes/depois.
- Garanta que todas as chaves de topo apareçam na ordem exata definida.

Agora gere o JSON conforme as regras acima.
PROMPT;


                        $resp = Http::withToken(env('OPENAI_API_KEY'))
                            ->acceptJson()
                            ->asJson()
                            ->post('https://api.openai.com/v1/responses', [
                                'model' => 'gpt-3.5-turbo',
                                'input' => [
                                    [
                                        'role'    => 'user',
                                        'content' => [
                                            ['type' => 'input_text', 'text' => $prompt],
                                            ['type' => 'input_file', 'file_id' => $openaiFileId],
                                        ],
                                    ],
                                ],
                                'temperature' => 0.2,
                            ]);

                        // captura para retorno imediato no modo debug
                        $openaiImmediateResponse = $resp->json();
                        $openaiImmediateStatus   = $resp->status();

                        // se NÃO estiver em debug, seguimos extraindo texto normalmente
                        if ($resp->successful() && !$debug) {
                            $curriculum = $this->parseOpenAIResponseText($openaiImmediateResponse);
                            if (is_string($curriculum)) {
                                $curriculum = preg_replace('/\s+/', ' ', trim($curriculum));
                            }
                        }
                    }

                    // Metadados do arquivo
                    $savedFileMeta = [
                        'path'     => $stored,
                        'mime'     => $mime,
                        'size'     => $file->getSize(),
                        'filename' => $file->getClientOriginalName(),
                    ];

                    // Salva no recruitment se existirem as colunas
                    $recruitmentUpdates = [];
                    if (Schema::hasColumn('recruitments', 'attachment_path')) $recruitmentUpdates['attachment_path'] = $stored;
                    if (Schema::hasColumn('recruitments', 'attachment_mime')) $recruitmentUpdates['attachment_mime'] = $mime;
                    if (Schema::hasColumn('recruitments', 'attachment_text') && !$debug) {
                        // grava o JSON/texto do "curriculum"
                        $recruitmentUpdates['attachment_text'] = $curriculum ?? '';
                    }
                    if (!empty($recruitmentUpdates)) $recruitment->update($recruitmentUpdates);
                }

                return ['person' => $person, 'recruitment' => $recruitment];
            });
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Falha ao criar pessoa/recruitment.',
                'trace' => app()->isProduction() ? null : $e->getMessage(),
            ], 500);
        }

        // ===== retorno imediato SOMENTE com a resposta da OpenAI quando debug=1 =====
        if ($debug) {
            if (!is_null($openaiImmediateResponse)) {
                return response()->json($openaiImmediateResponse, $openaiImmediateStatus ?? 200);
            }
            return response()->json([
                'error' => 'Debug=1 enviado, mas não houve resposta da OpenAI. Envie um arquivo em "attachment" (multipart/form-data).'
            ], 400);
        }

        // ===== fluxo normal (sem debug) =====
        $personId      = $result['person']->id_person ?? $result['person']->id;
        $recruitmentId = $result['recruitment']->id_recruitment ?? $result['recruitment']->id;

        // 5) Chama API externa de perfis/roles
        $payload = [
            'adjectives' => array_values($data['adjectives']),
            'skills'     => collect($data['skills'])->map(fn($s) => [
                'id'     => (int) $s['id'],
                'points' => (float) $s['points'],
            ])->values()->all(),
        ];

        try {
            $resp = Http::acceptJson()->timeout(15)
                ->post('https://api1.inperson.com.br/profiles/roles', $payload);

            $ext = $resp->json();

            if ($resp->failed()) {
                return response()->json([
                    'error'  => 'Falha ao consultar serviço externo.',
                    'status' => $resp->status(),
                    'body'   => $ext,
                ], 502);
            }

            $attributes = [
                'decision'   => data_get($ext, 'decision'),
                'detail'     => data_get($ext, 'detail'),
                'enthusiasm' => data_get($ext, 'enthusiasm'),
                'relational' => data_get($ext, 'relational'),
            ];

            $skillsNorm = collect(data_get($ext, 'skills', []))
                ->map(fn($s) => ['name' => data_get($s, 'name'), 'value' => data_get($s, 'value')])
                ->values()->all();

            $profileName = (string) data_get($ext, 'profile', '');

            try {
                CalculationResult::create([
                    'id_company'       => $idCompany,
                    'calculation_type' => 1,
                    'id_entity'        => $personId,
                    'response_time'    => null,
                    'request'          => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    'result_name'      => $profileName,
                    'result'           => json_encode($ext, JSON_UNESCAPED_UNICODE),
                    'attributes'       => json_encode($attributes, JSON_UNESCAPED_UNICODE),
                    'skills'           => json_encode($skillsNorm, JSON_UNESCAPED_UNICODE),
                    'calculed_at'      => now(),
                ]);
            } catch (\Throwable $e) {
                // não falha o fluxo por causa do log
            }

            return response()->json([
                'id_person'      => $personId,
                'id_recruitment' => $recruitmentId,
                'id_vacancy'     => $vacancyId,
                'result_name'    => $profileName,
                'attributes'     => $attributes,
                'skills'         => $skillsNorm,
                'file'           => $savedFileMeta,
                'curriculum'     => $curriculum, // <- novo nome no retorno
                'echo'           => [
                    'adjectives' => $payload['adjectives'],
                    'skills'     => $payload['skills'],
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Erro inesperado ao processar o cálculo externo.',
                'trace' => app()->isProduction() ? null : $e->getMessage(),
            ], 500);
        }
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

        $direct = data_get($json, 'output_text');
        if (is_string($direct) && trim($direct) !== '') return $direct;

        $output = data_get($json, 'output', []);
        if (is_array($output)) {
            foreach ($output as $block) {
                $contents = data_get($block, 'content', []);
                if (is_array($contents)) {
                    foreach ($contents as $c) {
                        $t = data_get($c, 'text') ?? data_get($c, 'value') ?? null;
                        if (is_string($t) && trim($t) !== '') return $t;
                    }
                }
            }
        }

        $choices = data_get($json, 'choices', []);
        if (is_array($choices) && isset($choices[0])) {
            $msg = data_get($choices[0], 'message.content');
            if (is_string($msg) && trim($msg) !== '') return $msg;
        }

        return null;
    }
}
