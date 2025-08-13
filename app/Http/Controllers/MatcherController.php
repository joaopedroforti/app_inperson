<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacancy;
use App\Models\Recruitment;
use App\Models\Department;
use App\Models\Person;
use App\Models\JobRole;
use App\Models\CalculationResult;

class MatcherController extends Controller
{
    public function index()
    {
        // Busca pessoas com cálculo tipo 1
        $personsWithCalc = Person::whereIn('id_person', function ($query) {
            $query->select('id_entity')
                ->from('calculation_results')
                ->where('calculation_type', 1);
        })->get(['id_person', 'full_name', 'cpf']);

        $persons = $personsWithCalc->map(function ($person) {
            $calculation = CalculationResult::where('id_entity', $person->id_person)
                ->where('calculation_type', 1)
                ->first();

            return [
                'full_name' => $person->full_name,
                'cpf' => $person->cpf,
                'attributes' => json_decode($calculation->attributes ?? '{}', true),
                'skills' => json_decode($calculation->skills ?? '[]', true),
                'profile' => json_decode($calculation->result ?? '{}')->profile ?? '',
                'result_name' => $calculation->result_name ?? '',
            ];
        });

        // Busca cargos com cálculo tipo 2
        $jobsWithCalc = JobRole::whereIn('id_job', function ($query) {
            $query->select('id_entity')
                ->from('calculation_results')
                ->where('calculation_type', 2);
        })->get();

        $jobs = $jobsWithCalc->map(function ($job) {
            $calculation = CalculationResult::where('id_entity', $job->id_job)
                ->where('calculation_type', 2)
                ->first();

            $decodedResult = json_decode($calculation->result ?? '{}', true);

            return [
                'job' => $job,
                'attributes' => json_decode($calculation->attributes ?? '{}', true),
                'skills' => $decodedResult['skills'] ?? [],
                'profile' => $decodedResult['profile'] ?? '',
                'result_name' => $calculation->result_name ?? '',
            ];
        });
        return view('content.matcher.index', [
            'persons' => $persons,
            'jobs' => $jobs,
        ]);
        return([
            'persons' => $persons,
            'jobs' => $jobs,
        ]);
    }
}
