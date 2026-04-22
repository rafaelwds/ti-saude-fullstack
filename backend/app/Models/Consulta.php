<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Consulta extends Model
{
    protected $fillable = [
        'cons_codigo',
        'data',
        'hora',
        'particular',
        'paciente_id',
        'medico_id',
        'plano_saude_id',
    ];

    protected function casts(): array
    {
        return [
            'particular' => 'boolean',
            'data'       => 'date:Y-m-d',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function planoSaude(): BelongsTo
    {
        return $this->belongsTo(PlanoSaude::class);
    }

    public function procedimentos(): BelongsToMany
    {
        return $this->belongsToMany(Procedimento::class, 'consulta_procedimento');
    }
}
