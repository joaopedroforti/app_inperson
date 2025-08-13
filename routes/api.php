<?php

use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\PublicVacancieController;

Route::middleware(['api.key'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);


    Route::get('/calculation-form', [PublicVacancieController::class, 'calculation']);
    Route::post('/candidate/new', [PublicVacancieController::class, 'candidate']);
});
