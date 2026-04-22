<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    protected $fillable = [
        'med_codigo',
        'med_nome',
        'med_crm',
        'especialidade_id',
    ];

    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }
}
