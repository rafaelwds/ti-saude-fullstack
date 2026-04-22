<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanoSaude extends Model
{
    protected $table = 'planos_saude';

    protected $fillable = [
        'plano_codigo',
        'plano_descricao',
        'plano_telefone',
    ];

    public function vinculos(): HasMany
    {
        return $this->hasMany(Vinculo::class);
    }
}
