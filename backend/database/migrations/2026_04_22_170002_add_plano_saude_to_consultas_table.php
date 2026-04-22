<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->foreignId('plano_saude_id')
                ->nullable()
                ->after('particular')
                ->constrained('planos_saude')
                ->nullOnDelete();

            $table->index('plano_saude_id');
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropForeign(['plano_saude_id']);
            $table->dropIndex(['plano_saude_id']);
            $table->dropColumn('plano_saude_id');
        });
    }
};
