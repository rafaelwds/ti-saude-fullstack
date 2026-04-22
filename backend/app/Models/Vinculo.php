<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vinculo extends Model
{
    protected $fillable = [
        'nr_contrato',
        'paciente_id',
        'plano_saude_id',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function planoSaude(): BelongsTo
    {
        return $this->belongsTo(PlanoSaude::class);
    }
}
