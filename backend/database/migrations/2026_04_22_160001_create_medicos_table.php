<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            $table->string('med_codigo', 20)->nullable()->unique();
            $table->string('med_nome', 255);
            $table->string('med_crm', 20)->unique();
            $table->foreignId('especialidade_id')
                ->constrained('especialidades')
                ->restrictOnDelete();
            $table->timestamps();

            $table->index('especialidade_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
