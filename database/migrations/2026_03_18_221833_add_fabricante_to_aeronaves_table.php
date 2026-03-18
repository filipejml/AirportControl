<?php
// database/migrations/2026_03_18_XXXXXX_add_fabricante_to_aeronaves_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->foreignId('fabricante_id')
                  ->after('capacidade')
                  ->nullable()
                  ->constrained('fabricantes')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->dropForeign(['fabricante_id']);
            $table->dropColumn('fabricante_id');
        });
    }
};