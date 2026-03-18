<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->enum('porte', ['PC', 'MC', 'LC'])
                  ->after('capacidade')
                  ->nullable()
                  ->comment('PC: Pequeno Porte (≤100), MC: Médio Porte (101-299), LC: Grande Porte (≥300)');
        });
    }

    public function down(): void
    {
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->dropColumn('porte');
        });
    }
};