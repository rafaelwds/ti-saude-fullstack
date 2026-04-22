<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimentos', function (Blueprint $table) {
            $table->id();
            $table->string('proc_codigo', 20)->nullable()->unique();
            $table->string('proc_nome', 255);
            $table->decimal('proc_valor', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimentos');
    }
};
