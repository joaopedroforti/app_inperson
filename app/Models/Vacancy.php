<?php

// app/Models/Vacancy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $table = 'vacancies';
    protected $primaryKey = 'id_vacancie';
    public $timestamps = false;

    protected $fillable = [
        'reference', 'description', 'id_job', 'id_company', 'vacancies_number',
        'type_vacancie', 'confidential', 'salary', 'benefits', 'seniority',
        'resume', 'activities', 'requirements', 'local', 'working_hours',
        'status', 'q1', 'q2', 'q3', 'q4', 'q5', 'expiration_date', 'creation_date', 'stars', 'questions'
    ];

    public function job()
    {
        return $this->belongsTo(JobRole::class, 'id_job', 'id_job');
    }

    public function jobRole()
{
    return $this->belongsTo(JobRole::class, 'id_job', 'id_job');
}

}
