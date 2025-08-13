<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRole extends Model
{
    protected $table = 'job_roles';
    protected $primaryKey = 'id_job';
    public $timestamps = false;

    protected $fillable = [
        'reference',
        'id_company',
        'id_department',
        'description',
        'seniority',
        'long_description',
        'status',
        'creation_date',
        'activities',
        'requirements',
    ];

    // Exemplo de relacionamento (opcional)
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department', 'id_department');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company', 'id_company');
    }
    public function calculationResultName()
{
    return $this->hasOne(\App\Models\CalculationResult::class, 'id_entity', 'id_job')
        ->where('calculation_type', 2);
}

}
