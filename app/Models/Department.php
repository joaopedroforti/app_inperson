<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'id_department';
    protected $fillable = ['description', 'id_manager', 'id_company', 'reference', 'status'];
    
    /**
     * Obtém o responsável (manager) associado ao departamento
     */
    public function manager()
    {
        return $this->belongsTo(Person::class, 'id_manager', 'id_person');
    }
    
    /**
     * Obtém os cargos associados a este departamento
     */
    public function jobRoles()
    {
        return $this->hasMany(JobRole::class, 'id_department', 'id_department');
    }
}
