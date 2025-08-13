<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occurrence extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'occurrences';

    /**
     * A chave primária associada à tabela.
     *
     * @var string
     */
    protected $primaryKey = 'id_occourrence';

    /**
     * Indica se o model deve ser timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'id_company',
        'id_person',
        'writer',
        'rule_writer',
        'text',
        'date'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Relacionamento com a empresa.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    /**
     * Relacionamento com a pessoa.
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }

    /**
     * Relacionamento com o escritor.
     */
    public function writer()
    {
        return $this->belongsTo(Person::class, 'id_writer');
    }
}
