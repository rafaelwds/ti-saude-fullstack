<?php

namespace Database\Seeders;

use App\Models\Procedimento;
use Illuminate\Database\Seeder;

class ProcedimentoSeeder extends Seeder
{
    public function run(): void
    {
        $procedimentos = [
            ['proc_codigo' => 'ECG',   'proc_nome' => 'Eletrocardiograma',                  'proc_valor' => 120.00],
            ['proc_codigo' => 'ECOCAR','proc_nome' => 'Ecocardiograma',                      'proc_valor' => 350.00],
            ['proc_codigo' => 'GLIC',  'proc_nome' => 'Glicemia em Jejum',                   'proc_valor' => 25.00],
            ['proc_codigo' => 'HEM',   'proc_nome' => 'Hemograma Completo',                  'proc_valor' => 45.00],
            ['proc_codigo' => 'URINA', 'proc_nome' => 'Urinálise (EAS)',                     'proc_valor' => 30.00],
            ['proc_codigo' => 'RX_TX', 'proc_nome' => 'Raio-X de Tórax',                     'proc_valor' => 80.00],
            ['proc_codigo' => 'ENDO',  'proc_nome' => 'Endoscopia Digestiva Alta',           'proc_valor' => 650.00],
            ['proc_codigo' => 'COLO',  'proc_nome' => 'Colonoscopia',                        'proc_valor' => 780.00],
            ['proc_codigo' => 'USG_AB','proc_nome' => 'Ultrassonografia de Abdômen Total',   'proc_valor' => 220.00],
            ['proc_codigo' => 'DERMO', 'proc_nome' => 'Dermatoscopia',                       'proc_valor' => 150.00],
            ['proc_codigo' => 'MAPA',  'proc_nome' => 'MAPA 24h (Monitoração Ambulatorial)', 'proc_valor' => 180.00],
            ['proc_codigo' => 'HOLTER','proc_nome' => 'Holter 24h',                          'proc_valor' => 200.00],
            ['proc_codigo' => 'TSH',   'proc_nome' => 'TSH (Tireoide)',                      'proc_valor' => 55.00],
            ['proc_codigo' => 'COLES', 'proc_nome' => 'Perfil Lipídico (Colesterol Total)',  'proc_valor' => 60.00],
            ['proc_codigo' => 'EEG',   'proc_nome' => 'Eletroencefalograma',                 'proc_valor' => 280.00],
        ];

        foreach ($procedimentos as $proc) {
            Procedimento::updateOrCreate(
                ['proc_codigo' => $proc['proc_codigo']],
                [
                    'proc_nome'  => $proc['proc_nome'],
                    'proc_valor' => $proc['proc_valor'],
                ]
            );
        }
    }
}
