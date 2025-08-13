<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Vacancy;
use App\Models\Recruitment;
use App\Models\Company;
use App\Models\Department;
use App\Models\JobRole;
use App\Models\Person;
use App\Models\Occurrence;
use App\Models\CalculationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
class VacancyController extends Controller

{
    public function getperson(string $reference)
    {

 $recruitment = Recruitment::where('id_recruitment', $reference)
 ->select('recruitments.*')
 ->get();
$id_person = $recruitment['0']->id_person;

     $person = Person::where('id_person',$id_person)
         ->where('status', 'active')
         ->select([
             'id_person',
             'id_company',
             'person_type',
             'status',
             'step',
             'full_name',
             'corporate_email',
             'birth_date',
             'id_gender',
             'id_marital_status',
             'id_education_level',
             'id_disability_type',
             'nationality',
             'foreigner',
             'father_name',
             'mother_name',
             'country',
             'zip_code',
             'address_number',
             'address_district',
             'address_city',
             'address_state',
             'address_complement',
             'cellphone',
             'phone',
             'emergency_phone',
             'personal_email',
             'department',
             'role',
             'contract_type',
             'admission_date',
             'registration_number',
             'experience_period',
             'contract_date',
             'contract_expiration_date',
             'cpf',
             'cnpj',
             'foreigner_document',
             'rg',
             'rg_issue_date',
             'rg_issuer',
             'cnh',
             'military_certificate',
             'pis',
             'bank',
             'agency',
             'account',
             'pix_key',
         ])
         ->first();
 
     // Busca apenas os campos necessários da calculation
     $calculation = CalculationResult::where([
         ['calculation_type', '=', 1],
         ['id_entity', '=', $id_person]
     ])->select('result_name', 'attributes', 'skills', 'calculed_at')->first();
 
     // Busca ocorrências do tipo RH
     $rh_occurrences = Occurrence::where('id_person', $id_person)
         ->where('id_company', $person->id_company)
         ->where('rule_writer', 'RH')
         ->get();
 
     // Busca ocorrências do tipo GESTOR
     $gestor_occurrences = Occurrence::where('id_person', $id_person)
         ->where('id_company', $person->id_company)
         ->where('rule_writer', 'Gestor')
         ->get();
 
     $candidates[] = [
         'recruitment' => $recruitment,
         'person' => $person,
         'calculation_result' => $calculation,
         'rh_occurrences' => $rh_occurrences,
         'gestor_occurrences' => $gestor_occurrences,
     ];
     return $candidates
;    }
    public function list()
    {
        $id_company = session('company_id');
        $vacancies = Vacancy::with('job')
        ->select('id_vacancie', 'description', 'reference', 'status', 'creation_date', 'expiration_date', 'id_job', 'confidential')
        ->where('id_company', $id_company) // Filtro da empresa aqui
        ->get()
        ->map(function ($vacancy) {
            // Contando os recrutamentos ligados a esta vaga
            $recruitmentCount = Recruitment::where('id_vacancy', $vacancy->id_vacancie)->count();
    

        return [
            'reference' => $vacancy->reference,
            'id' => $vacancy->id_vacancie,
            'nome_vaga' => $vacancy->description,
            'status' => $vacancy->status,
            'data_criacao' => date('d/m/Y', strtotime($vacancy->creation_date)),
            'data_encerramento' => $vacancy->expiration_date ? date('d/m/Y H:i', strtotime($vacancy->expiration_date)) : null,
            'departamento' => $vacancy->job->description ?? 'N/A',
            'confidential' => $vacancy->confidential,
            'recruitmentCount' => $recruitmentCount
        ];
    });

            return view('content.vacancy.list', compact('vacancies'));
    }
    public function new()
    {
        //Verifica limite de Vagas ativas do plano
        $companyId = session('company_id');
        $company = Company::find($companyId);
        $activeVacanciesCount = Vacancy::where('id_company', $companyId)
        ->where('status', 1)
        ->where('expiration_date', '>', Carbon::now())
        ->count();
        $maxVacancies = $company->plan_config['limits']['active_vacancies']['max'] ?? 0;
        if ($activeVacanciesCount >= $maxVacancies) {
            return redirect()->route('Aviso', [
                'title' => 'Módulo de Vagas',
                'text' => 'Você atingiu o limite de vagas ativas.'
            ]);
                                                    }




        $departments = Department::where('status', 1)
    ->where('id_company', session('company_id'))
    ->get();

    
        return view('content.vacancy.new', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $id_company = session('company_id');
        $dados = $request->all();
    
        // Closure recursivo para detectar "vazio" (inclui HTML vazio do TinyMCE)
        $isBlank = null;
        $isBlank = function ($v) use (&$isBlank): bool {
            if (is_null($v)) return true;
    
            if (is_string($v)) {
                $raw = trim($v);
                if ($raw === '' || strtolower($raw) === 'null') return true;
                // Remove tags/espacos e verifica se ficou vazio (ex: "<p><br></p>")
                $plain = trim(strip_tags($v));
                return $plain === '';
            }
    
            if (is_array($v)) {
                foreach ($v as $x) {
                    if (!$isBlank($x ?? null)) return false;
                }
                return true;
            }
    
            return false;
        };
    
        // 1) Monta payload ignorando tudo que é "vazio" e "Selecionar"
        $dados_filtrados = collect($dados)
            ->reject($isBlank)
            ->reject(fn($v) => $v === 'Selecionar')
            ->toArray();
    
        // 2) Empresa sempre
        $dados_filtrados['id_company'] = $id_company;
    
        // 3) Se vier id_job, buscar seniority
        $idJob = $dados['id_job'] ?? null;
        if (!empty($idJob)) {
            if ($job = JobRole::where('id_job', $idJob)->first()) {
                $dados_filtrados['seniority'] = $job->seniority;
            }
        }
    
        // 4) Mapear campos opcionais -> nomes finais (apenas se não-vazios)
        $map = [
            'descricao_vaga'         => 'resume',
            'atividades'             => 'activities',
            'requisitos'             => 'requirements',
            'workplace_type'         => 'local',
            'data_encerramento_vaga' => 'expiration_date',
        ];
        foreach ($map as $origem => $destino) {
            if (array_key_exists($origem, $dados) && !$isBlank($dados[$origem])) {
                $dados_filtrados[$destino] = $dados[$origem];
            }
        }
    
        // 5) Perguntas (JSON string), só se não-vazio
        if (array_key_exists('perguntas_json', $dados) && !$isBlank($dados['perguntas_json'])) {
            $dados_filtrados['questions'] = $dados['perguntas_json'];
        }
    
        // 6) Defaults (se não vier do form)
        $dados_filtrados['status']        = $dados_filtrados['status']        ?? 1;
        $dados_filtrados['type_vacancie'] = $dados_filtrados['type_vacancie'] ?? 1;
    
        // 7) Upsert por reference + company (sem sobrescrever com vazio, pois já filtramos)
        $existingVacancy = Vacancy::where('reference', $request->input('reference'))
            ->where('id_company', $id_company)
            ->first();
    
        if ($existingVacancy) {
            $existingVacancy->update($dados_filtrados);
            return redirect('vacancy/list')->with('success', 'Vaga atualizada com sucesso!');
        }
    
        Vacancy::create($dados_filtrados);
        return redirect('vacancy/list')->with('success', 'Vaga criada com sucesso!');
    }
    
   
    
    public function candidates(string $reference)
{
    // Busca a vaga pela referência
    $vacancy = Vacancy::where('reference', $reference)->first();

    if (!$vacancy) {
        return [
            'success' => false,
            'message' => 'Vaga não encontrada.',
        ];
    }
    $vacancycalculation = CalculationResult::where([
        ['calculation_type', '=', 2],
        ['id_entity', '=', $vacancy->id_job]
    ])->select('result_name', 'attributes', 'skills', 'calculed_at')->first();
    

    // Conta os recrutamentos da vaga
    $recruitmentCount = Recruitment::where('id_vacancy', $vacancy->id_vacancie)->count();

    // Busca todos os recrutamentos da vaga
    $recruitments = Recruitment::join('persons', 'recruitments.id_person', '=', 'persons.id_person')
    ->where('recruitments.id_vacancy', $vacancy->id_vacancie)
    ->where('persons.status', 'active')
    ->select('recruitments.*')
    ->get();

    $candidates = [];

    foreach ($recruitments as $recruitment) {
        $person = Person::where('id_person', $recruitment->id_person)
            ->where('status', 'active')
            ->select([
                'id_person',
                'stars',
                'id_company',
                'person_type',
                'status',
                'step',
                'full_name',
                'corporate_email',
                'birth_date',
                'id_gender',
                'id_marital_status',
                'id_education_level',
                'id_disability_type',
                'nationality',
                'foreigner',
                'father_name',
                'mother_name',
                'country',
                'zip_code',
                'address_number',
                'address_district',
                'address_city',
                'address_state',
                'address_complement',
                'cellphone',
                'phone',
                'emergency_phone',
                'personal_email',
                'department',
                'role',
                'contract_type',
                'admission_date',
                'registration_number',
                'experience_period',
                'contract_date',
                'contract_expiration_date',
                'cpf',
                'cnpj',
                'foreigner_document',
                'rg',
                'rg_issue_date',
                'rg_issuer',
                'cnh',
                'military_certificate',
                'pis',
                'bank',
                'agency',
                'account',
                'pix_key',
            ])
            ->first();
    
        // Busca apenas os campos necessários da calculation
        $calculation = CalculationResult::where([
            ['calculation_type', '=', 1],
            ['id_entity', '=', $recruitment->id_person]
        ])->select('result_name', 'attributes', 'skills', 'calculed_at')->first();
    
        // Busca ocorrências do tipo RH
        $rh_occurrences = Occurrence::where('id_person', $recruitment->id_person)
            ->where('id_company', $person->id_company)
            ->where('rule_writer', 'RH')
            ->get();
    
        // Busca ocorrências do tipo GESTOR
        $gestor_occurrences = Occurrence::where('id_person', $recruitment->id_person)
            ->where('id_company', $person->id_company)
            ->where('rule_writer', 'Gestor')
            ->get();
    
        $candidates[] = [
            'recruitment' => $recruitment,
            'person' => $person,
            'calculation_result' => $calculation,
            'rh_occurrences' => $rh_occurrences,
            'gestor_occurrences' => $gestor_occurrences,
        ];
    }
    
    // Retorno completo
    return view('content.vacancy.candidates', [
        'vacancycalculation' => $vacancycalculation,
        'success' => true,
        'vacancy' => $vacancy,
        'recruitment_count' => $recruitmentCount,
        'departments' => '1',
        'candidates' => $candidates,
    ]);
}






public function edit(string $reference)
{
    // Busca a vaga pela referência
    $vacancy = Vacancy::where('reference', $reference)->first();
    $job = JobRole::where('id_job', $vacancy->id_job)->firstOrFail();

    $department = Department::where('id_department', $job->id_department)->firstOrFail();






    if (!$vacancy) {
        return [
            'success' => false,
            'message' => 'Vaga não encontrada.',
        ];
    }
    // Retorno completo
    return view('content.vacancy.edit', [
        'vacancy' => $vacancy, 
        'job' => $job, 
        'department' => $department, 
    ]);
}







public function competencias(string $reference)
{
    $vacancy = Vacancy::where('reference', $reference)->firstOrFail();

    $calculation = CalculationResult::where([
        ['calculation_type', '=', 2],
        ['id_entity', '=', $vacancy->id_job]
    ])->select('result_name', 'attributes', 'skills', 'calculed_at')->first();

    $vacancy->calculation = $calculation;

    return view('content.vacancy.competences', [
        'vacancy' => $vacancy,
        'calculation' => $calculation,
    ]);
}














public function buscar(Request $request)
{
    $id_company = session('company_id');
    $company =  Company::where('id_company', $id_company)->first();
    $cep = $company->zip_code;
    $reference = $request->input('reference');
    $input = $request->input('search'); // Captura o texto de busca do usuário
    // Busca a vaga pela referência
    $vacancy = Vacancy::where('reference', $reference)->first();

    if (!$vacancy) {
        return [
            'success' => false,
            'message' => 'Vaga não encontrada.',
        ];
    }

    // Monta a query base com os joins necessários e distinct para evitar duplicatas
    $query = Recruitment::join('persons', 'recruitments.id_person', '=', 'persons.id_person')
        ->leftJoin('calculation_results', function ($join) {
            $join->on('calculation_results.id_entity', '=', 'recruitments.id_person')
                 ->where('calculation_results.calculation_type', '=', 1);
        })
        ->where('recruitments.id_vacancy', $vacancy->id_vacancie)
        ->where('persons.status', 'active')
        ->select('recruitments.*')
        ->distinct(); // Garante que não haja duplicatas em recruitments

    // Aplica os filtros dinâmicos, se houver texto de busca
    if ($input) {
        $prompt = <<<EOT
Você é um assistente PHP para um sistema de recrutamento Laravel. Sua tarefa é converter uma string de busca do usuário (ex.: "{$input}") em um **array PHP de cláusulas `where()`** para uma query Eloquent. Os termos da string são separados por espaços, e cada termo deve ser classificado e mapeado para cláusulas conforme as regras abaixo.

### Contexto da Query
- Tabela principal: `recruitments`
- Joins:
  ```sql
  JOIN persons ON persons.id_person = recruitments.id_person
  LEFT JOIN calculation_results ON calculation_results.id_entity = recruitments.id_person
    AND calculation_results.calculation_type = 1
  ```
- Cláusula fixa já aplicada:
  ```php
  ['recruitments.id_vacancy', '=', {$vacancy->id_vacancie}]
  ```

### Saída Esperada
Retorne **apenas** um array PHP de cláusulas adicionais no formato:
```php
[
  ['campo', 'operador', 'valor'],
  ['campo', 'operador', 'valor', 'or'], // 'or' indica orWhere
]
```
- O array deve ser compatível com `Recruitment::query()->where(...)->orWhere(...)`.
- Sanitize os termos de busca para evitar SQL injection (ex.: use `addslashes` para valores `LIKE`).
- Normalize termos para estados e cidades (ex.: remova acentos usando `Str::ascii` do Laravel).

### Classificação de Termos
Cada termo da string de busca deve ser classificado em um dos tipos abaixo e gerar cláusulas correspondentes:

| Tipo                     | Campo Alvo                                                      | Regras Específicas                                                                 |
|--------------------------|-----------------------------------------------------------------|-----------------------------------------------------------------------------------|
| **Nome**                 | `persons.full_name` LIKE '%termo%'                              | Não gera cláusula em `curriculum`. Identificar nomes próprios (ex.: "João", "Maria"). |
| **Perfil Comportamental** | `calculation_results.result_name` LIKE '%termo%'                | Termos: "Relacional", "Decisão", "Entusiasmo", "Detalhismo". Mapear adjetivos (ex.: "Entusiasta" → "Entusiasmo", "Decisões" → "Decisão", "Detalhista" → "Detalhismo"). A IA deve inferir a forma base do adjetivo. |
| **Idade**                | `persons.birth_date` <= Carbon::today()->subYears(\$idade)       | Extrair número (ex.: "20 anos" → 20). Validar que é um número inteiro positivo. Calcular data usando `Carbon::today()->subYears(\$idade)->toDateString()`. |
| **Cidade**               | `persons.address_city` LIKE '%termo%'<br>`recruitments.curriculum` LIKE '%termo%' | Gera cláusulas com `OR` (ex.: `[..., 'or']` para `curriculum`). Buscar com e sem acentos (ex.: "Santa Bárbara" e "Santa Barbara"). |
| **Estado**               | `persons.address_state` LIKE '%termo%'                         | Buscar termo, termo sem acentos (via `Str::ascii`), e sigla (ex.: "São Paulo", "Sao Paulo", "SP"). |
| **Distância (CEP)**      | `persons.zip_code` BETWEEN 'cep_inicial' AND 'cep_final'     | Quando o termo indicar algo como "raio,dentro,até, outros termos de X km", calcule uma faixa de CEP usando o valor central {$cep}. A lógica é: arredondar ± (X km × 7), considerando que aproximadamente 7 CEPs cobrem 1km. Por exemplo: raio de 10km → deslocamento de 70 CEPs → faixa de {$cep}-70 até {$cep}+70. Gere cláusula assim: ['persons.address_cep', 'BETWEEN', ['13450435', '13450575']]|
| **Formação/Experiência ou Outro Termo**          | `recruitments.curriculum` LIKE '%termo%'<br>`recruitments.curriculum` LIKE '%sinônimo1%'<br>`recruitments.curriculum` LIKE '%sinônimo2%' | A IA deve *SEMPRE* inferir sinônimos contextuais e versões femininas ou netras(se existir)(ex.: "vendas" → "vendedor", "comercial", "vendedora"; "marketing" → "mkt", "publicidade"). Usar no minimo 5 sinônimos e no máximo 10 sinônimos por termo. |

### Regras Adicionais?
##Sempre que for algo relacionado a escola, formação ou curso, deve buscar em recruitments.curriculum.
exemplo: Colégio Técnico de Limeira - busca somente no curriculum, e nao na address_city.
1. **Cláusulas em `curriculum`**: Todo termo, exceto "nome" e "perfil comportamental", gera pelo menos uma cláusula em `recruitments.curriculum` with 3 variações (termo original + 2 sinônimos inferidos pela IA).
2. **Cláusulas OR**: Se um termo gera cláusulas em múltiplos campos (ex.: cidade), usar `orWhere` (adicionando `'or'` como quarto elemento).
3. **Normalização**:
   - Para estados e cidades, buscar:
     - Termo original (ex.: "São Paulo").
     - Termo sem acentos (ex.: "Sao Paulo").
     - Sigla, se aplicável (ex.: "SP").
   - Usar `Str::ascii` para normalizar acentos.
4. **Sinônimos**: 
6. **Evitar duplicatas**: Não gerar cláusulas idênticas para o mesmo campo e valor.
7. **Validação**:
   - Ignorar termos inválidos (ex.: vazios, muito curtos como "e", "de").
   - Para idade, validar que é um número positivo. Se inválido, ignorar o termo.
8. **Performance**: Assumir índices em `full_name`, `address_city`, `address_state`, e `curriculum` para buscas `LIKE`.

### Exemplos
**Entrada**: "Pessoas que moram em Santa Bárbara d'Oeste e possuem mais de 20 anos"
**Saída**:
```php
[
  ['persons.address_city', 'LIKE', '%Santa Bárbara d\'Oeste%'],
  ['persons.address_city', 'LIKE', '%Santa Barbara d\'Oeste%'],
  ['recruitments.curriculum', 'LIKE', '%Santa Bárbara d\'Oeste%', 'or'],
  ['recruitments.curriculum', 'LIKE', '%Santa Barbara d\'Oeste%', 'or'],
  ['persons.birth_date', '<=', '2005-08-06']
]
```

**Entrada**: "vendedor decisão São Paulo"
**Saída**:
```php
[
  ['recruitments.curriculum', 'LIKE', '%vendedor%'],
  ['recruitments.curriculum', 'LIKE', '%vendas%'],
  ['recruitments.curriculum', 'LIKE', '%comercial%'],
  ['calculation_results.result_name', 'LIKE', '%Decisão%'],
  ['persons.address_state', 'LIKE', '%São Paulo%'],
  ['persons.address_state', 'LIKE', '%Sao Paulo%'],
  ['persons.address_state', 'LIKE', '%SP%']
]
```

**Entrada**: " campinas detalhista"
```php
[
  ['persons.address_city', 'LIKE', '%Campinas%'],
  ['persons.address_city', 'LIKE', '%Campinas%'],
  ['recruitments.curriculum', 'LIKE', '%Campinas%', 'or'],
  ['recruitments.curriculum', 'LIKE', '%Campinas%', 'or'],
  ['calculation_results.result_name', 'LIKE', '%Detalhismo%']
]
```

### Notas Finais
- **Múltiplos termos**: Termos são combinados com `AND` na query, exceto quando especificado `orWhere` dentro de um mesmo termo.
- **Acentuação**: Sempre incluir cláusulas com e sem acentos para todos os termos.
- **Perfil comportamental**: Mapear adjetivos para a forma base (ex.: "Entusiasta" → "Entusiasmo", "Decisões" → "Decisão").
- **Idade**: Calcular `persons.birth_date` usando `Carbon::today()->subYears(\$idade)->toDateString()` para garantir formato `YYYY-MM-DD`.
- Não deve retornar nenhum texto ou informação, a nao ser a saida previamente informada. não retornar nenhum texto como "Aqui está a saída esperada para a string de busca "Pessoas até 10km de distancia"
Entrada do usuário:
{$input}
EOT;

        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-3.5-turbo',
                    'messages'    => [ ['role' => 'user', 'content' => $prompt] ],
                    'temperature' => 0,
                ]);

            if ($response->failed()) {
                \Log::error('Erro OpenAI: ' . json_encode($response->json()));
                return [
                    'success' => false,
                    'message' => 'Erro ao processar filtros de busca.',
                ];
            }

            // Extrai o conteúdo da resposta
$whereArrayCode = $response->json('choices.0.message.content');
// Remove Markdown code fences (```php
$whereArrayCode = preg_replace('/^```php\s*|\s*```$/', '', trim($whereArrayCode));  
            $filters = eval("return {$whereArrayCode};");
dd($filters);
            // Aplica os filtros na query
            $query->where(function ($subQuery) use ($filters) {
                foreach ($filters as $filter) {
                    $field = $filter[0];
                    $operator = strtoupper($filter[1]);
                    $value = $filter[2];
                    $logicalOperator = isset($filter[3]) && $filter[3] === 'or' ? 'orWhere' : 'where';
            
                    if ($operator === 'BETWEEN' && is_array($value)) {
                        $subQuery->{$logicalOperator . 'Between'}($field, $value);
                    } else {
                        $subQuery->$logicalOperator($field, $operator, $value);
                    }
                }
            });
            
        } catch (\Exception $e) {
            \Log::error('Erro ao processar filtros: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar filtros de busca.',
            ];
        }
    }
    // Conta os recrutamentos da vaga
    $recruitmentCount = $query->count();

    // Executa a query para buscar os recrutamentos
    $recruitments = $query->get();
    $candidates = [];
    //dd(vsprintf(str_replace('?', "'%s'", $query->toSql()), $query->getBindings()));

    foreach ($recruitments as $recruitment) {
        $person = Person::where('id_person', $recruitment->id_person)
            ->where('status', 'active')
            ->select([
                'id_person',
                'stars',
                'id_company',
                'person_type',
                'status',
                'step',
                'full_name',
                'corporate_email',
                'birth_date',
                'id_gender',
                'id_marital_status',
                'id_education_level',
                'id_disability_type',
                'nationality',
                'foreigner',
                'father_name',
                'mother_name',
                'country',
                'zip_code',
                'address_number',
                'address_district',
                'address_city',
                'address_state',
                'address_complement',
                'cellphone',
                'phone',
                'emergency_phone',
                'personal_email',
                'department',
                'role',
                'contract_type',
                'admission_date',
                'registration_number',
                'experience_period',
                'contract_date',
                'contract_expiration_date',
                'cpf',
                'cnpj',
                'foreigner_document',
                'rg',
                'rg_issue_date',
                'rg_issuer',
                'cnh',
                'military_certificate',
                'pis',
                'bank',
                'agency',
                'account',
                'pix_key',
            ])
            ->first();

        // Busca apenas os campos necessários da calculation
        $calculation = CalculationResult::where([
            ['calculation_type', '=', 1],
            ['id_entity', '=', $recruitment->id_person]
        ])->select('result_name', 'attributes', 'skills', 'calculed_at')->first();

        // Busca ocorrências do tipo RH
        $rh_occurrences = Occurrence::where('id_person', $recruitment->id_person)
            ->where('id_company', $person->id_company)
            ->where('rule_writer', 'RH')
            ->get();

        // Busca ocorrências do tipo GESTOR
        $gestor_occurrences = Occurrence::where('id_person', $recruitment->id_person)
            ->where('id_company', $person->id_company)
            ->where('rule_writer', 'Gestor')
            ->get();

        $candidates[] = [
            'recruitment' => $recruitment,
            'person' => $person,
            'calculation_result' => $calculation,
            'rh_occurrences' => $rh_occurrences,
            'gestor_occurrences' => $gestor_occurrences,
        ];
    }
    $vacancycalculation = CalculationResult::where([
        ['calculation_type', '=', 2],
        ['id_entity', '=', $vacancy->id_job]
    ])->select('result_name', 'attributes', 'skills', 'calculed_at')->first();
    // Retorno completo


    return view('content.vacancy.candidates', [
        'input' => $input,
        'vacancycalculation' => $vacancycalculation,
        'success' => true,
        'vacancy' => $vacancy,
        'recruitment_count' => $recruitmentCount,
        'departments' => '1', // Revisar: este valor parece fixo, talvez precise de ajuste
        'candidates' => $candidates,
    ]);
}

























}
