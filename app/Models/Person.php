<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    protected $primaryKey = 'id_person';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_company',
        'person_type',
        'status',
        'full_name',
        'corporate_email',
        'birth_date',
        'id_gender',
        'photo',
        'id_marital_status',
        'id_education_level',
        'id_disability_type',
        'nationality',
        'foreigner',
        'father_name',
        'mother_name',
        'country',
        'zip_code',
        'address_number',
        'address_district',
        'address_city',
        'address_state',
        'address_complement',
        'cellphone',
        'phone',
        'emergency_phone',
        'personal_email',
        'department',
        'role',
        'contract_type',
        'admission_date',
        'registration_number',
        'experience_period',
        'contract_date',
        'contract_expiration_date',
        'cpf',
        'cnpj',
        'foreigner_document',
        'rg',
        'rg_issue_date',
        'rg_issuer',
        'cnh',
        'military_certificate',
        'pis',
        'bank',
        'agency',
        'account',
        'pix_key',
        'step',
        'profile_pic_base64'
    ];
}
