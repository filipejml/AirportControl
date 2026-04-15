<?php
// database/migrations/2026_04_10_000001_modify_depositos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            // Remover campos que não serão mais usados
            if (Schema::hasColumn('depositos', 'codigo')) {
                $table->dropColumn('codigo');
            }
            if (Schema::hasColumn('depositos', 'localizacao')) {
                $table->dropColumn('localizacao');
            }
            if (Schema::hasColumn('depositos', 'area_total')) {
                $table->dropColumn('area_total');
            }
            if (Schema::hasColumn('depositos', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            $table->string('codigo')->nullable();
            $table->string('localizacao')->nullable();
            $table->decimal('area_total', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
        });
    }
};