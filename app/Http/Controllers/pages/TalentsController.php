<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Department;
use App\Models\CalculationResult;
use App\Models\Recruitment;
use App\Models\JobRole;
use Illuminate\Support\Facades\Crypt;
class TalentsController extends Controller
{
 
    public function index()
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
        'id_person' => $person->id_person,
          'profile_pic_base64' => $person->profile_pic_base64,
          'id_company' => $person->id_company,
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
  
  return view('content.talents.list', compact('persons'));
  
    }






    public function edit($encryptedId)
    {
        try {
            $id_person = Crypt::decryptString($encryptedId);
        } catch (\Exception $e) {
            abort(404, 'ID inválido');
        }
    
        $id_company = session('company_id');
    
        $person = Person::where('id_company', $id_company)
                        ->where('id_person', $id_person)
                        ->first();
    
        if ($person) {
            $departments = Department::where('status', 1)
                ->where('id_company', $id_company)
                ->get();
            return view('content.talents.edit', compact('person', 'departments'));
        } else {
            return abort(404, 'Pessoa não encontrada');
        }

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
      $dados_filtrados['person_type'] = 'candidate';
      $dados_filtrados['id_company'] = $id_company;



      if (!empty($id_person)) {
          $existingPerson = Person::where('id_company', $id_company)
              ->where('id_person', $id_person)
              ->first();
  
          if ($existingPerson) {
              $existingPerson->update($dados_filtrados);
              return redirect('bancotalentos')->with('success', 'Atualizado com sucesso');
          }
      }
  
      Person::create($dados_filtrados);
      
      return redirect('bancotalentos')->with('success', 'Cadastrado com sucesso');
  }
  

  











  public function profile($encryptedId)
  {
      $id_company = session('company_id');
      try {
        $id_person = Crypt::decryptString($encryptedId);
    } catch (\Exception $e) {
        abort(404, 'ID inválido');
    }

      // Verifica se existe pessoa com esse CPF e empresa
      $person = Person::where('id_company', $id_company)
                      ->where('id_person', $id_person)
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


          return view('content.talents.profile', compact('relatorios', 'person', 'lastCalculation'));
      } else {
          // Caso a pessoa não seja encontrada
          return response()->json(['message' => 'Pessoa não encontrada.'], 404);
      }
  }




  public function historico($encryptedId)
  {
      $id_company = session('company_id');
  
      try {
          $id_person = Crypt::decryptString($encryptedId);
      } catch (\Exception $e) {
          abort(404, 'ID inválido');
      }
  
      // Confere pessoa da empresa
      $person = Person::where('id_company', $id_company)
          ->where('id_person', $id_person)
          ->first();
  
      if (!$person) {
          abort(404, 'Pessoa não encontrada.');
      }
  
      // Recruitments do candidato + descrição da vaga (vacancies.description)
      // Observação: em seu projeto, a PK de vacancies aparece como id_vacancie.
      // Mantive essa coluna no join conforme seu padrão anterior.
      $recruitments = Recruitment::select(
              'recruitments.*',
              'vacancies.description as vacancy_description'
          )
          ->leftJoin('vacancies', 'vacancies.id_vacancie', '=', 'recruitments.id_vacancy')
          ->where('recruitments.id_company', $id_company)
          ->where('recruitments.id_person', $id_person)
          ->orderByDesc('recruitments.creation_date')
          ->get();
  
      return view('content.talents.historico', compact('person', 'recruitments'));
  }











  
}
