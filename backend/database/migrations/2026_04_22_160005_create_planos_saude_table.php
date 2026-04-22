<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos_saude', function (Blueprint $table) {
            $table->id();
            $table->string('plano_codigo', 20)->nullable()->unique();
            $table->string('plano_descricao', 255);
            $table->string('plano_telefone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planos_saude');
    }
};
