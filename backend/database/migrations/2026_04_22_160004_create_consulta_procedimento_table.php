<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_procedimento', function (Blueprint $table) {
            $table->foreignId('consulta_id')
                ->constrained('consultas')
                ->cascadeOnDelete();
            $table->foreignId('procedimento_id')
                ->constrained('procedimentos')
                ->restrictOnDelete();

            $table->primary(['consulta_id', 'procedimento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_procedimento');
    }
};
