<?php


// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $primaryKey = 'id_company';
    protected $table = 'companies';

    protected $fillable = [
        'company_name',
        'document_number',
        'status',
        'country',
        'zip_code',
        'address_street',
        'address_number',
        'address_district',
        'address_complement',
        'address_city',
        'address_state',
	'webhook_link',
	'api_key',
    ];
    protected $casts = [
        'plan_config' => 'array',
    ];
    
}
