<?php

namespace Database\Seeders;

use App\Models\Especialidade;
use Illuminate\Database\Seeder;

class EspecialidadeSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            ['espec_codigo' => 'CARD', 'espec_nome' => 'Cardiologia'],
            ['espec_codigo' => 'DERM', 'espec_nome' => 'Dermatologia'],
            ['espec_codigo' => 'ENDO', 'espec_nome' => 'Endocrinologia'],
            ['espec_codigo' => 'GAST', 'espec_nome' => 'Gastroenterologia'],
            ['espec_codigo' => 'NEUR', 'espec_nome' => 'Neurologia'],
            ['espec_codigo' => 'ORTO', 'espec_nome' => 'Ortopedia'],
            ['espec_codigo' => 'PEDI', 'espec_nome' => 'Pediatria'],
            ['espec_codigo' => 'PSIQ', 'espec_nome' => 'Psiquiatria'],
        ];

        foreach ($especialidades as $especialidade) {
            Especialidade::updateOrCreate(
                ['espec_codigo' => $especialidade['espec_codigo']],
                ['espec_nome'   => $especialidade['espec_nome']]
            );
        }
    }
}
