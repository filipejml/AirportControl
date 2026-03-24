<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companhias_aereas', function (Blueprint $table) {
            $table->string('codigo', 10)->nullable()->after('nome');
            $table->index('codigo');
        });
    }

    public function down()
    {
        Schema::table('companhias_aereas', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};