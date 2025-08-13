<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacancy;
use App\Models\Recruitment;
use App\Models\Department;
use App\Models\Person;
use App\Models\Occurrence;
use App\Models\CalculationResult;
use App\Models\JobRole;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        
        $id_company = session('company_id');

        $qtdmasculino = Person::where('id_gender', 1)
            ->where('person_type', 'collaborator')
            ->where('id_company', $id_company)
            ->count();

        $qtdfeminino = Person::where('id_gender', 2)
            ->where('person_type', 'collaborator')
            ->where('id_company', $id_company)
            ->count();

        $qtdoutros = Person::where('id_gender', 3)
            ->where('person_type', 'collaborator')
            ->where('id_company', $id_company)
            ->count();

        $activejobs = JobRole::where('id_company', $id_company)
            ->where('status', 1)
            ->count();

        $activevacancies = Vacancy::where('id_company', $id_company)
            ->where('status', 1)
            ->where('expiration_date', '>', Carbon::now())
            ->count();

            $qtdcandidates = Person::where('person_type', 'candidate')
            ->where('id_company', $id_company)
            ->whereIn('id_person', function ($query) {
                $query->select('id_person')->from('recruitments');
            })->count();
        

        $qtdtalents = Person::where('person_type', 'candidate')
            ->where('id_company', $id_company)
            ->count();
            $vacancies = Vacancy::with(['jobRole.department'])
            ->where('id_company', $id_company)
            ->orderByDesc('creation_date')
            ->take(5)
            ->get()
            ->map(function ($vacancy) {
                $recruitmentCount = Recruitment::where('id_vacancy', $vacancy->id_vacancie)->count();
        
                return [
                    'status' => $vacancy->status,
                    'creation_date' => $vacancy->creation_date,
                    'description' => $vacancy->description,
                    'recruitment_count' => $recruitmentCount,
                    'expiration_date' => $vacancy->expiration_date,
                    'department' => optional(optional($vacancy->jobRole)->department)->description,
                    'confidential' => $vacancy->confidential,
                ];
            });
        

            $candidates = Recruitment::orderByDesc('creation_date')
            ->take(5)
            ->get()
            ->map(function ($recruitment) {
                $person = Person::where('id_person', $recruitment->id_person)->first();
        
                $calc = CalculationResult::where('id_entity', $recruitment->id_person)
                    ->where('calculation_type', 1)
                    ->first();
        
                return [
                    'name' => $person->full_name ?? '',
                    'step' => $person->step ?? '',
                    'recruitment_date' => $recruitment->creation_date,
                    'result_name' => $calc->result_name ?? null,
                ];
            });
        

        return view('dashboard', compact(
            'qtdmasculino',
            'qtdfeminino',
            'qtdoutros',
            'activejobs',
            'activevacancies',
            'qtdcandidates',
            'qtdtalents',
            'vacancies',
            'candidates'
        ));
    }

    public function treinamentos()
    {
return view('treinamentos');
    }


}
