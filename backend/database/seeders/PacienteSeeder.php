<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            [
                'nome'            => 'Ana Paula Ferreira',
                'data_nascimento' => '1985-03-14',
                'telefones'       => ['81999110001', '81988220002'],
            ],
            [
                'nome'            => 'Bruno Costa Lima',
                'data_nascimento' => '1992-07-22',
                'telefones'       => ['81999330003'],
            ],
            [
                'nome'            => 'Carla Mendes Souza',
                'data_nascimento' => '1978-11-05',
                'telefones'       => ['81999440004', '81933550005'],
            ],
            [
                'nome'            => 'Diego Alves Rocha',
                'data_nascimento' => '2001-01-30',
                'telefones'       => ['81999660006'],
            ],
            [
                'nome'            => 'Fernanda Oliveira',
                'data_nascimento' => '1995-09-18',
                'telefones'       => ['81999770007', '81988880008'],
            ],
        ];

        foreach ($pacientes as $dados) {
            $paciente = Paciente::updateOrCreate(
                ['nome' => $dados['nome']],
                ['data_nascimento' => $dados['data_nascimento']]
            );

            // Adiciona telefones que ainda não existam
            foreach ($dados['telefones'] as $numero) {
                $paciente->telefones()->firstOrCreate(['telefone' => $numero]);
            }
        }
    }
}
