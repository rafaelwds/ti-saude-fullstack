<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Procedimento extends Model
{
    protected $fillable = [
        'proc_codigo',
        'proc_nome',
        'proc_valor',
    ];

    protected function casts(): array
    {
        return [
            'proc_valor' => 'decimal:2',
        ];
    }

    public function consultas(): BelongsToMany
    {
        return $this->belongsToMany(Consulta::class, 'consulta_procedimento');
    }
}
