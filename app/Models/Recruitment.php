<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    protected $table = 'recruitments';
    protected $primaryKey = 'id_recruitment';
    public $timestamps = false;

    protected $fillable = [
        'id_company',
        'id_person',
        'id_vacancy',
        'questions',
        'curriculum',
        'interview',
        'creation_date',
    ];

    // Se quiser relacionamentos:

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company', 'id_company');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_person', 'id_person');
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'id_vacancy', 'id_vacancie');
    }
}
