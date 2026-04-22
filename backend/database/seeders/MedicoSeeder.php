<?php

namespace Database\Seeders;

use App\Models\Especialidade;
use App\Models\Medico;
use Illuminate\Database\Seeder;

class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        $cardiologia = Especialidade::where('espec_codigo', 'CARD')->first();
        $pediatria   = Especialidade::where('espec_codigo', 'PEDI')->first();

        if ($cardiologia) {
            Medico::updateOrCreate(
                ['med_crm' => 'CRM-PE 12345'],
                [
                    'med_codigo'      => 'MED001',
                    'med_nome'        => 'Dr. Carlos Eduardo Souza',
                    'especialidade_id' => $cardiologia->id,
                ]
            );
        }

        if ($pediatria) {
            Medico::updateOrCreate(
                ['med_crm' => 'CRM-PE 67890'],
                [
                    'med_codigo'      => 'MED002',
                    'med_nome'        => 'Dra. Ana Paula Lima',
                    'especialidade_id' => $pediatria->id,
                ]
            );
        }
    }
}
