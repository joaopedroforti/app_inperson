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
Extraia TODO o conteúdo textual do arquivo anexado e devolva como um texto corrido em UTF-8, sem cabeçalhos/rodapés repetidos, preservando a ordem dos trechos, sem comentários adicionais.

Analise texto gerado. Extraia as informações principais e organize-as em um JSON com os campos pré-definidos abaixo. Cada campo deve corresponder aos dados fornecidos no texto, e qualquer informação extra deve ser colocada em 'additional_info'. 

Evite incluir informações de contato, endereço ou data de nascimento em additional_info. Para 'career_summary', gere um breve resumo imparcial com base nas informações disponíveis sobre as experiências, habilidades e qualificações acadêmicas da pessoa. Use a seguinte estrutura:

{
    "person_document": null,
    "reference": null,
    "job_position": null,
    "salary_expectation": null,
    "experiences": [
        {
            "position": null,
            "company": null,
            "start_date": null,
            "end_date": null,
            "description": null
        }
    ],
    "skills": null,
    "academic_qualifications": [
        {
            "course_name": null,
            "institution": null,
            "year_of_completion": null
        }
    ],
    "languages": [
        {
            "language": null,
            "level": null
        }
    ],
    "additional_info": null,
    "career_summary": null,
    "profile_and_competencies": null,
    "courses_and_certifications": [
        {
            "course": null,
            "date": null
        }
    ],
    "technical_skills": null
}
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
