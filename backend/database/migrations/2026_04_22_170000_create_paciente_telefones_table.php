<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_telefones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();
            $table->string('telefone', 20);
            $table->timestamps();

            $table->index('paciente_id');
        });

        // Migra o campo telefone existente em pacientes para a nova tabela
        DB::table('pacientes')
            ->whereNotNull('telefone')
            ->where('telefone', '!=', '')
            ->get(['id', 'telefone'])
            ->each(function ($paciente) {
                DB::table('paciente_telefones')->insert([
                    'paciente_id' => $paciente->id,
                    'telefone'    => $paciente->telefone,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_telefones');
    }
};
