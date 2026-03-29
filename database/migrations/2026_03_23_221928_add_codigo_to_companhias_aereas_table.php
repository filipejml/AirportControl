<?php
// database/migrations/2026_03_23_221928_add_codigo_to_companhias_aereas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companhias_aereas', function (Blueprint $table) {
            // Alterar para NOT NULL e remover nullable
            $table->string('codigo', 10)->nullable(false)->change();
            $table->index('codigo');
        });
    }

    public function down()
    {
        Schema::table('companhias_aereas', function (Blueprint $table) {
            $table->string('codigo', 10)->nullable()->change();
        });
    }
};