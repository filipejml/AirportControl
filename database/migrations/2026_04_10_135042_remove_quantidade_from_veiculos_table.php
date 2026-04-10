<?php
// database/migrations/2026_04_10_000002_remove_quantidade_from_veiculos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            if (Schema::hasColumn('veiculos', 'quantidade')) {
                $table->dropColumn('quantidade');
            }
            if (Schema::hasColumn('veiculos', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->integer('quantidade')->default(1);
            $table->text('observacoes')->nullable();
        });
    }
};