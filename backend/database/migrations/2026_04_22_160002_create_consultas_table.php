<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('cons_codigo', 20)->nullable()->unique();
            $table->date('data');
            $table->time('hora');
            $table->boolean('particular')->default(false);
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->restrictOnDelete();
            $table->foreignId('medico_id')
                ->constrained('medicos')
                ->restrictOnDelete();
            $table->timestamps();

            $table->index(['paciente_id', 'data']);
            $table->index('medico_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
