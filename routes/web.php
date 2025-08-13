<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\pages\EmployeeController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\pages\TalentsController;
use App\Http\Controllers\MatcherController;
use App\Http\Controllers\OccurrenceController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui estão as rotas para login, logout, e as rotas protegidas.
|
*/
// Main Page Route
Route::get('/buscar-cep/{cep}', [CepController::class, 'buscar']);
Route::post('/api/occurrences', [OccurrenceController::class, 'store']);
Route::put('/api/occurrences/{id_occourrence}', [OccurrenceController::class, 'update']);
Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');
Route::get('/api/job-roles/{id}/long-description', [JobRoleController::class, 'getLongDescription']);
// locale
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// pages
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('test', [AuthController::class, 'test'])->name('test');

// Rota para login
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);

// Rota para logout
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('Dashboard');
 Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard');


    Route::get('/treinamentos', [DashboardController::class, 'treinamentos'])->name('Treinamentos');
    Route::get('/employee/new', [EmployeeController::class, 'index'])->name('Novo Colaborador');
    Route::get('/candidate/{id_person}', [TalentsController::class, 'edit'])->name('Perfil Candidato');
    Route::get('/candidate/profile/{id_person}', [TalentsController::class, 'profile'])->name('Perfil Comportamental Candidato');
    Route::get('/candidate/historico/{id_person}', [TalentsController::class, 'historico'])->name('Historico do Candidato');

    Route::get('/employee/edit/{cpf}', [EmployeeController::class, 'update'])->name('Perfil');
    Route::post('/employee/new', [EmployeeController::class, 'store'])->name('employee-new');
    Route::post('/candidate/new', [TalentsController::class, 'store'])->name('employee-new');
    Route::get('/employee/profile/{cpf}', [EmployeeController::class, 'profile'])->name('Perfil Comportamental');
    Route::get('/employee/list', [EmployeeController::class, 'list'])->name('Colaboradores');
    Route::get('/bancotalentos', [TalentsController::class, 'index'])->name('Banco de Talentos');
    Route::get('/vacancy/list', [VacancyController::class, 'list'])->name('Vagas');
    Route::get('/vacancy/new', [VacancyController::class, 'new'])->name('Nova Vaga');
    Route::post('/vacancy/new', [VacancyController::class, 'store'])->name('vacancy-store');
    Route::get('/departments/{id}/job-roles', [JobRoleController::class, 'getByDepartment']);
    Route::get('/vacancy/{reference}', [VacancyController::class, 'candidates'])->name('Vaga');
    Route::get('/vacancy/edit/{reference}', [VacancyController::class, 'edit'])->name('EditarVaga');
    Route::get('/vacancy/competences/{reference}', [VacancyController::class, 'competencias'])->name('Roda de Competencias');
    // routes/web.php
Route::get('/empresa/{id}/endereco', [CompanyController::class, 'endereco']);
Route::get('/job/new', [JobRoleController::class, 'new'])->name('Adicionar Cargo');
Route::get('/jobrole/edit/{reference}', [JobRoleController::class, 'edit'])->name('Editar Cargo');
Route::post('/job/new', [JobRoleController::class, 'store'])->name('Cargos');
Route::get('/job', [JobRoleController::class, 'list'])->name('Cargos');
Route::get('/matcher', [MatcherController::class, 'index'])->name('Matcher');
Route::post('updatestep/{cpf}/{step}', [EmployeeController::class, 'updateStep']);
Route::get('/department/new', [JobRoleController::class, 'newdepartment'])->name('Novo Departamento');
Route::get('/department/{reference}', [JobRoleController::class, 'editdepartment'])->name('Editar Departamento');
Route::post('/department/new', [JobRoleController::class, 'storedepartment']);
Route::get('/departments', [JobRoleController::class, 'departmentlist'])->name('Departamentos');
Route::post('updatestatus/{cpf}/{status}', [EmployeeController::class, 'updateStatus']);
Route::post('updatestars/{cpf}/{star}', [EmployeeController::class, 'updatestars']);
Route::get('/settings', [CompanyController::class, 'index'])->name('Configurações');
Route::post('/settings', [CompanyController::class, 'update'])->name('settings.update');


Route::get('/getperson/{reference}', [VacancyController::class, 'getperson']);
Route::post('/pesquisa/candidatos', [VacancyController::class, 'buscar'])->name('Pesquisa Avançada');
Route::get('/pesquisa/candidatos', [VacancyController::class, 'buscar'])->name('Pesquisa Avançada');








Route::get('/erro/acesso', function () {
    return view('errors.access_blocked', [
        'title' => request('title'),
        'text' => request('text'),
        'button_text' => request('button'),
        'button_link' => request('link')
    ]);
})->name('Aviso');




});
