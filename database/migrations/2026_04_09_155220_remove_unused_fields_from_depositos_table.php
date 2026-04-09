<?php
// database/migrations/2026_04_09_000001_remove_unused_fields_from_depositos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            // Remover campos que não são mais necessários
            $table->dropColumn(['codigo', 'localizacao', 'area_total']);
        });
    }

    public function down(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            // Recriar campos caso seja necessário reverter
            $table->string('codigo')->unique()->after('nome');
            $table->string('localizacao')->nullable()->after('codigo');
            $table->decimal('area_total', 10, 2)->nullable()->after('localizacao');
        });
    }
};