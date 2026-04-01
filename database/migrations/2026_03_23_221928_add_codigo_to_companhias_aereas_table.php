<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companhias_aereas', function (Blueprint $table) {
            // First add the column if it doesn't exist
            if (!Schema::hasColumn('companhias_aereas', 'codigo')) {
                $table->string('codigo', 10)->nullable()->after('nome');
            }
            
            // Then add the index
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('companhias_aereas', function (Blueprint $table) {
            $table->dropIndex(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};