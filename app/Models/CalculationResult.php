<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    protected $table = 'calculation_results';
    protected $primaryKey = 'id_calculation';
    public $timestamps = false;

    protected $fillable = [
        'reference',
        'id_company',
        'calculation_type',
        'id_entity',
        'response_time',
        'request',
        'result_name',
        'result',
        'attributes',
        'skills',
        'calculed_at',
        'created_at'
    ];
}
