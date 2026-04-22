<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vinculos', function (Blueprint $table) {
            $table->id();
            $table->string('nr_contrato', 50);
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();
            $table->foreignId('plano_saude_id')
                ->constrained('planos_saude')
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(['paciente_id', 'plano_saude_id']);
            $table->index('plano_saude_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vinculos');
    }
};
