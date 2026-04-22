<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\PlanoSaude;
use App\Models\Vinculo;
use Illuminate\Database\Seeder;

class VinculoSeeder extends Seeder
{
    public function run(): void
    {
        $ana      = Paciente::where('nome', 'Ana Paula Ferreira')->first();
        $bruno    = Paciente::where('nome', 'Bruno Costa Lima')->first();
        $carla    = Paciente::where('nome', 'Carla Mendes Souza')->first();
        $diego    = Paciente::where('nome', 'Diego Alves Rocha')->first();
        $fernanda = Paciente::where('nome', 'Fernanda Oliveira')->first();

        $unimed    = PlanoSaude::where('plano_codigo', 'UNIMED')->first();
        $amil      = PlanoSaude::where('plano_codigo', 'AMIL')->first();
        $bradesco  = PlanoSaude::where('plano_codigo', 'BRADESCO')->first();
        $hapvida   = PlanoSaude::where('plano_codigo', 'HAPVIDA')->first();
        $sulamerica = PlanoSaude::where('plano_codigo', 'SULAMERICA')->first();

        $vinculos = [
            ['paciente' => $ana,      'plano' => $unimed,    'nr_contrato' => 'UNI-2024-001'],
            ['paciente' => $ana,      'plano' => $bradesco,  'nr_contrato' => 'BRAD-2023-045'],
            ['paciente' => $bruno,    'plano' => $amil,      'nr_contrato' => 'AMIL-2025-112'],
            ['paciente' => $carla,    'plano' => $hapvida,   'nr_contrato' => 'HAP-2022-330'],
            ['paciente' => $carla,    'plano' => $sulamerica,'nr_contrato' => 'SUL-2024-789'],
            ['paciente' => $diego,    'plano' => $unimed,    'nr_contrato' => 'UNI-2026-005'],
            ['paciente' => $fernanda, 'plano' => $amil,      'nr_contrato' => 'AMIL-2024-567'],
        ];

        foreach ($vinculos as $v) {
            if (!$v['paciente'] || !$v['plano']) {
                continue;
            }

            Vinculo::updateOrCreate(
                [
                    'paciente_id'    => $v['paciente']->id,
                    'plano_saude_id' => $v['plano']->id,
                ],
                ['nr_contrato' => $v['nr_contrato']]
            );
        }
    }
}
