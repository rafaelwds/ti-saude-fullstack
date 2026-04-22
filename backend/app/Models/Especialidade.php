<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidade extends Model
{
    protected $fillable = [
        'espec_codigo',
        'espec_nome',
    ];

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }
}
