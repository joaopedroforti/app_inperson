<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Department;
use App\Models\CalculationResult;
use App\Models\Recruitment;
use App\Models\JobRole;
class EmployeeController extends Controller
{
  public function index()
  {

    $departments = Department::where('status', 1)
    ->where('id_company', session('company_id'))
    ->get();
    return view('content.employees.new', compact('departments'));

  }

  public function list()
  {
    
    $id_company = session('company_id');
    $collaborators = Person::where('id_company', $id_company)
    ->where('person_type', 'collaborator')
    ->get();

$persons = [];

foreach ($collaborators as $person) {
    $lastCalculation = CalculationResult::where('id_company', $id_company)
    ->where('calculation_type', 1)
        ->where('id_entity', $person->id_person)
        ->orderByDesc('calculed_at')
        ->first();
        $jobDescription = JobRole::where('id_job', $person->role)->value('description');

        $Department = Department::where('id_department', $person->department)->value('description');

    $persons[] = [
        'profile_pic_base64' => $person->profile_pic_base64,
        'full_name' => $person->full_name,
        'cpf' => $person->cpf,
        'department' => $Department,
        'role' => $jobDescription,
        'result_name' => $lastCalculation ? $lastCalculation->result_name : null,
    ];
}
return view('content.employees.list', compact('persons'));

  }


  public function listtalentos()
  {
    
    $id_company = session('company_id');
    $collaborators = Person::where('id_company', $id_company)
    ->where('person_type', 'candidate')
    ->get();

$persons = [];

foreach ($collaborators as $person) {
    $lastCalculation = CalculationResult::where('id_company', $id_company)
    ->where('calculation_type', 1)
        ->where('id_entity', $person->id_person)
        ->orderByDesc('calculed_at')
        ->first();

        $countvagas = Recruitment::where('id_person', $person->id_person)->count();

    $persons[] = [
        'profile_pic_base64' => $person->profile_pic_base64,
        'full_name' => $person->full_name,
        'cpf' => $person->cpf,
        'department' => $person->department,
        'role' => $person->role,
        'result_name' => $lastCalculation ? $lastCalculation->result_name : null,
        'created_at' => $person->created_at->format('d/m/Y'),
        'cidadeestado' => $person->address_city . ' - ' . $person->address_state,
        'countvagas' => $countvagas,
    ];
}

return view('content.talentos.list', compact('persons'));

  }




  public function store(Request $request)
  {
      $id_company = session('company_id');
      $id_person = $request->input('id_person');

      $dados = $request->all();
      
      $dados_filtrados = collect($dados)->filter(function ($valor, $chave) {
          return $valor !== 'Selecionar' && $chave !== 'foreigner';
      })->toArray();

    $dados_filtrados['profile_pic_base64'] = $request->input('profile_pic_base64');
      $dados_filtrados['department'] = $request->input('id_department');
      $dados_filtrados['id_disability_type'] = $request->input('is_pcd') ? null : $request->input('id_disability_type', null);
      $dados_filtrados['foreigner'] = $request->has('foreigner') ? 'on' : null;
      $dados_filtrados['person_type'] = 'collaborator';
      $dados_filtrados['id_company'] = $id_company;



      if (!empty($id_person)) {
          $existingPerson = Person::where('id_company', $id_company)
              ->where('id_person', $id_person)
              ->first();
  
          if ($existingPerson) {
              $existingPerson->update($dados_filtrados);
              return redirect('employee/list')->with('success', 'Atualizado com sucesso');
          }
      }
  
      Person::create($dados_filtrados);
      
      return redirect('employee/list')->with('success', 'Cadastrado com sucesso');
  }
  

  
  public function update($cpf)
  { 
    $id_company = session('company_id');

    // Verifica se existe pessoa com esse CPF e empresa
    $person = Person::where('id_company', $id_company)
                    ->where('cpf', $cpf)
                    ->first();

    // Se encontrou, exibe os dados
    if ($person) {
        
    $departments = Department::where('status', 1)
    ->where('id_company', session('company_id'))
    ->get();
        return view('content.employees.edit', compact('person', 'departments'));
    } else {
        echo "Pessoa não encontrada.";
    }
  }
  public function profile($cpf)
  {
      $id_company = session('company_id');
  
      // Verifica se existe pessoa com esse CPF e empresa
      $person = Person::where('id_company', $id_company)
                      ->where('cpf', $cpf)
                      ->first();
  
      if ($person) {
          // Busca o último cálculo associado à pessoa e à empresa
          $lastCalculation = CalculationResult::where('id_company', $id_company)
                                            ->where('calculation_type', 1 )
                                              ->where('id_entity', $person->id_person)
                                              ->orderByDesc('calculed_at')
                                              ->first();

                                              $relatorios = CalculationResult::where('id_company', $id_company)
                                              ->where('calculation_type', 1)
                                              ->where('id_entity', $person->id_person)
                                              ->orderBy('calculed_at', 'desc')
                                              ->paginate(5);
          // Passa os dados para a vie/


          return view('content.employees.profile', compact('relatorios', 'person', 'lastCalculation'));
      } else {
          // Caso a pessoa não seja encontrada
          return response()->json(['message' => 'Pessoa não encontrada.'], 404);
      }
  }
  public function updateStep($cpf, $step)
  {
      $id_company = session('company_id');
  
      // Atualiza o campo 'step' onde o cpf for igual e pertencer à empresa logada
      $updated = \App\Models\Person::where('cpf', $cpf)
          ->where('id_company', $id_company)
          ->update(['step' => $step]);
  
      if ($updated) {
          return response()->json(['success' => true, 'message' => 'Etapa atualizada com sucesso.']);
      }
  
      return response()->json(['success' => false, 'message' => 'Funcionário não encontrado ou erro ao atualizar.'], 404);
  }
  
  public function updateStatus($cpf, $status)
  {
      $id_company = session('company_id');
  
      // Atualiza o campo 'step' onde o cpf for igual e pertencer à empresa logada
      $updated = \App\Models\Person::where('cpf', $cpf)
          ->where('id_company', $id_company)
          ->update(['status' => $status]);
  
      if ($updated) {
          return response()->json(['success' => true, 'message' => 'Etapa atualizada com sucesso.']);
      }
  
      return response()->json(['success' => false, 'message' => 'Funcionário não encontrado ou erro ao atualizar.'], 404);
  }

  public function updatestars($cpf, $star)
  {
      $id_company = session('company_id');
  
      // Atualiza o campo 'step' onde o cpf for igual e pertencer à empresa logada
      $updated = \App\Models\Person::where('cpf', $cpf)
          ->where('id_company', $id_company)
          ->update(['stars' => $star]);
  
      if ($updated) {
          return response()->json(['success' => true, 'message' => 'Estrela.']);
      }
  
      return response()->json(['success' => false, 'message' => 'Funcionário não encontrado ou erro ao atualizar.'], 404);
  }



}
