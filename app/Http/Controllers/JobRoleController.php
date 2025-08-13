<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobRole;
use App\Models\Person;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\CalculationResult;
class JobRoleController extends Controller
{
    public function new()
    {
        $departments = Department::where('status', 1)->get();
    
        // Obter skills com cache
        $skills = cache()->get('skills_cache');
        if ($skills === null) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api1.inperson.com.br/profiles/skills",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => ["User-Agent: insomnia/9.3.3"],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    
            $skills = [];
    
            if (!$err) {
                $dataArray = json_decode($response, true);
                if (isset($dataArray["data"])) {
                    for ($i = 0; $i < count($dataArray["data"]); $i += 2) {
                        $pair = [
                            "skill1id" => $dataArray["data"][$i]["id"],
                            "skill1description" => $dataArray["data"][$i]["description"],
                        ];
                        if (isset($dataArray["data"][$i + 1])) {
                            $pair["skill2id"] = $dataArray["data"][$i + 1]["id"];
                            $pair["skill2description"] = $dataArray["data"][$i + 1]["description"];
                        }
                        $skills[] = $pair;
                    }
                }
            }
    
            // Salvar no cache por 5 minutos (300 segundos)
            cache()->put('skills_cache', $skills, 300);
        }
    
        // Obter adjectives com cache
        $adjectives = cache()->get('adjectives_cache');
        if ($adjectives === null) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api1.inperson.com.br/profiles/adjectives",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => ["User-Agent: insomnia/9.3.3"],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    
            $adjectives = [];
    
            if (!$err) {
                $dataArray = json_decode($response, true);
                if (isset($dataArray["data"])) {
                    foreach ($dataArray["data"] as $item) {
                        $adjectives[] = [
                            "id" => $item["id"],
                            "description" => $item["description"],
                        ];
                    }
                }
            }
    
            // Salvar no cache por 5 minutos (300 segundos)
            cache()->put('adjectives_cache', $adjectives, 300);
        }
    
        return view('content.jobrole.new', [
            'departments' => $departments,
            'skills' => $skills,
            'adjectives' => $adjectives
        ]);
    }
    









    public function edit(string $reference)
    {

        $jobRoles = JobRole::with([
            'department:id_department,description',
            'calculationResultName'
        ])->where('reference', $reference)->first();
        
        $department = Department::where('id_department', $jobRoles->id_department)->first();
        $departments = Department::where('status', 1)->get();
    
        // Obter skills com cache
        $skills = cache()->get('skills_cache');
        if ($skills === null) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api1.inperson.com.br/profiles/skills",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => ["User-Agent: insomnia/9.3.3"],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    
            $skills = [];
    
            if (!$err) {
                $dataArray = json_decode($response, true);
                if (isset($dataArray["data"])) {
                    for ($i = 0; $i < count($dataArray["data"]); $i += 2) {
                        $pair = [
                            "skill1id" => $dataArray["data"][$i]["id"],
                            "skill1description" => $dataArray["data"][$i]["description"],
                        ];
                        if (isset($dataArray["data"][$i + 1])) {
                            $pair["skill2id"] = $dataArray["data"][$i + 1]["id"];
                            $pair["skill2description"] = $dataArray["data"][$i + 1]["description"];
                        }
                        $skills[] = $pair;
                    }
                }
            }
    
            // Salvar no cache por 5 minutos (300 segundos)
            cache()->put('skills_cache', $skills, 300);
        }
    
        // Obter adjectives com cache
        $adjectives = cache()->get('adjectives_cache');
        if ($adjectives === null) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api1.inperson.com.br/profiles/adjectives",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => ["User-Agent: insomnia/9.3.3"],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    
            $adjectives = [];
    
            if (!$err) {
                $dataArray = json_decode($response, true);
                if (isset($dataArray["data"])) {
                    foreach ($dataArray["data"] as $item) {
                        $adjectives[] = [
                            "id" => $item["id"],
                            "description" => $item["description"],
                        ];
                    }
                }
            }
    
            // Salvar no cache por 5 minutos (300 segundos)
            cache()->put('adjectives_cache', $adjectives, 300);
        }

        $calculation = CalculationResult::where('calculation_type', 2)
        ->where('id_entity', $jobRoles->id_job)
        ->orderByDesc('calculed_at')
        ->first() ?? null;
    

        return view('content.jobrole.edit', [
            'departments'  => $departments,
            'skills'       => $skills,
            'adjectives'   => $adjectives,
            'job'          => $jobRoles,
            'departmento'  => $department,
            'calculation'  => $calculation
        ]);
        
    }















    
    public function getByDepartment($id)
    {
        $jobRoles = JobRole::where('id_department', $id)
            ->where('status', 1)
            ->get(['id_job', 'description']);

        return response()->json($jobRoles);
    }
    public function store(Request $request)
    {
        $id_company = session('company_id');
    $post = $request->all();

    // Verifica se há referência e tenta encontrar o JobRole
    $jobRole = null;
    if (!empty($post['reference'])) {
        $jobRole = JobRole::where('reference', $post['reference'])->first();
    }

    // Se não encontrou, cria um novo
    if (!$jobRole) {
        $jobRole = new JobRole();
        $jobRole->creation_date = now(); // apenas novo
        $jobRole->reference = $post['reference'] ?? uniqid(); // define reference se for novo
    }

    // Preenche os dados (comum a novo e existente)
    $jobRole->id_company = $id_company;
    $jobRole->id_department = $post['id_department'] ?? null;
    $jobRole->description = $post['description'] ?? null;
    $jobRole->long_description = $post['resume'] ?? null;
    $jobRole->activities = $post['activities'] ?? null;
    $jobRole->requirements = $post['requirements'] ?? null;
    $jobRole->seniority = $post['seniority'] ?? null;
    $jobRole->status = 1;
    $jobRole->save();
    
        // Decodificar resultado da API
        $result = json_decode($post["response"], true);
    
        $decision = $result["decision"] ?? null;
        $detail = $result["detail"] ?? null;
        $enthusiasm = $result["enthusiasm"] ?? null;
        $relational = $result["relational"] ?? null;
        $profile = str_replace(",", " e ", $result["profile"] ?? '');
    
        $attributes = json_encode([
            "decision" => $decision,
            "detail" => $detail,
            "enthusiasm" => $enthusiasm,
            "relational" => $relational,
        ], JSON_UNESCAPED_UNICODE);
    
        $skills = [];
        if (isset($result["skills"]) && is_array($result["skills"])) {
            foreach ($result["skills"] as $skill) {
                $skills[] = [
                    "name" => $skill["name"] ?? '',
                    "value" => $skill["value"] ?? '',
                ];
            }
        }
        $skills = json_encode($skills, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Criar CalculationResult
        $calculation = new CalculationResult();
        $calculation->reference = $post['reference'] ?? null;
        $calculation->id_company = $id_company;
        $calculation->calculation_type = 2;
        $calculation->id_entity = $jobRole->id_job;
        $calculation->response_time = 1;  // pode ajustar se tiver outro valor
        $calculation->request = json_encode($post, JSON_UNESCAPED_UNICODE);
        $calculation->result_name = $profile;
        $calculation->result = $post["response"];
        $calculation->attributes = $attributes;
        $calculation->skills = $skills;
        $calculation->calculed_at = now();
        $calculation->created_at = now();
        $calculation->save();
    
        $jobRoles = JobRole::with([
            'department:id_department,description',
            'calculationResultName'
        ])->get();
    
        return view('content.jobrole.list', compact('jobRoles'));
    }
    public function calculationResultName()
    {
        return $this->hasOne(CalculationResult::class, 'id_entity', 'id_job')
            ->select('id_entity', 'result_name')
            ->where('calculation_type', 2);
    }

    public function list()
{
    // Buscar job roles com a descrição do department e o result_name da calculation_results
    $jobRoles = JobRole::with([
        'department:id_department,description',
        'calculationResultName'
    ])->get();

    return view('content.jobrole.list', compact('jobRoles'));
}

public function newdepartment()
{
    $id_company = session('company_id');
    $persons = Person::where('id_company', $id_company)
                    ->where('person_type', 'collaborator')
                    ->get();
    
    return view('content.jobrole.newdepartment', compact('persons'));
}
public function storedepartment(Request $request)
{

  
    $validated = $request->validate([
        'reference'   => 'nullable|string|max:255',
        'description' => 'required|string|max:255',
        'id_manager'  => 'required|exists:persons,id_person',
    ]);
 

    // Verifica se é edição (tem reference)
    if (!empty($request['reference'])) {
        $department = Department::where('reference', $request['reference'])->firstOrFail();

        $department->update([
            'description' => $validated['description'],
            'id_manager'  => $validated['id_manager'],
        ]);

        return redirect()->route('Departamentos')
                         ->with('success', 'Departamento atualizado com sucesso!');
    }

    // Caso contrário, cria novo
    Department::create([
        'description' => $validated['description'],
        'id_manager'  => $validated['id_manager'],
        'id_company'  => session('company_id'),
    ]);

    return redirect()->route('Departamentos')
                     ->with('success', 'Departamento criado com sucesso!');

}




    public function departmentlist()
    {
        $departments = DB::table('departments')
        ->leftJoin('persons', 'departments.id_manager', '=', 'persons.id_person')
        ->select('departments.*', 'persons.full_name as responsavel')
        ->get();
        return view('content.jobrole.departments', compact('departments'));
    }
    public function editdepartment($reference)
    {

        $id_company = session('company_id');
        $department = Department::where('reference', $reference)->firstOrFail();

        $persons = Person::where('id_company', $id_company)
        ->where('person_type', 'collaborator')
        ->get();
        return view('content.jobrole.editdep', compact('department', 'persons'));
    }
    
    public function getLongDescription($id)
    {
        try {
            $jobRole = JobRole::find($id);

            if (!$jobRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cargo não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'long_description' => $jobRole->long_description,
                'activities' => $jobRole->activities,
                'requirements' => $jobRole->requirements
            ], 200);
            

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar long_description do cargo: ' . $e->getMessage(), ['id_job' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao buscar descrição do cargo.'
            ], 500);
        }
    }
}