<?php

namespace Database\Seeders;

use App\Models\PlanoSaude;
use Illuminate\Database\Seeder;

class PlanoSaudeSeeder extends Seeder
{
    public function run(): void
    {
        $planos = [
            [
                'plano_codigo'    => 'UNIMED',
                'plano_descricao' => 'Unimed Recife',
                'plano_telefone'  => '08007222066',
            ],
            [
                'plano_codigo'    => 'AMIL',
                'plano_descricao' => 'Amil Saúde',
                'plano_telefone'  => '08007226576',
            ],
            [
                'plano_codigo'    => 'BRADESCO',
                'plano_descricao' => 'Bradesco Saúde',
                'plano_telefone'  => '08007260948',
            ],
            [
                'plano_codigo'    => 'SULAMERICA',
                'plano_descricao' => 'SulAmérica Saúde',
                'plano_telefone'  => '08007252732',
            ],
            [
                'plano_codigo'    => 'HAPVIDA',
                'plano_descricao' => 'Hapvida NotreDame Intermédica',
                'plano_telefone'  => '08007072767',
            ],
            [
                'plano_codigo'    => 'NOTREDAME',
                'plano_descricao' => 'NotreDame Intermédica',
                'plano_telefone'  => '08002754848',
            ],
            [
                'plano_codigo'    => 'CASSI',
                'plano_descricao' => 'CASSI (Banco do Brasil)',
                'plano_telefone'  => '08007224411',
            ],
            [
                'plano_codigo'    => 'GEAP',
                'plano_descricao' => 'GEAP Saúde (Servidores Federais)',
                'plano_telefone'  => '08007274327',
            ],
        ];

        foreach ($planos as $plano) {
            PlanoSaude::updateOrCreate(
                ['plano_codigo' => $plano['plano_codigo']],
                [
                    'plano_descricao' => $plano['plano_descricao'],
                    'plano_telefone'  => $plano['plano_telefone'],
                ]
            );
        }
    }
}
