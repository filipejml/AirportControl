<?php
// database/migrations/2026_03_18_XXXXXX_create_fabricantes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabricantes', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('pais_origem')->nullable();
            $table->timestamps();
        });

        // Inserir os fabricantes da lista
        $fabricantes = [
            ['nome' => 'Airbus', 'pais_origem' => 'França'],
            ['nome' => 'Boeing', 'pais_origem' => 'EUA'],
            ['nome' => 'Embraer', 'pais_origem' => 'Brasil'],
            ['nome' => 'Bombardier Aerospace', 'pais_origem' => 'Canadá'],
            ['nome' => 'De Havilland Canada', 'pais_origem' => 'Canadá'],
            ['nome' => 'ATR', 'pais_origem' => 'França/Itália'],
            ['nome' => 'BAE Systems', 'pais_origem' => 'Reino Unido'],
            ['nome' => 'Dornier', 'pais_origem' => 'Alemanha'],
            ['nome' => 'Fairchild Dornier', 'pais_origem' => 'Alemanha/EUA'],
            ['nome' => 'Fokker', 'pais_origem' => 'Países Baixos'],
            ['nome' => 'McDonnell Douglas', 'pais_origem' => 'EUA'],
            ['nome' => 'Saab', 'pais_origem' => 'Suécia'],
            ['nome' => 'Douglas Aircraft Company', 'pais_origem' => 'EUA'],
            ['nome' => 'Convair', 'pais_origem' => 'EUA'],
            ['nome' => 'Aérospatiale/BAC', 'pais_origem' => 'França/Reino Unido'],
        ];

        foreach ($fabricantes as $fabricante) {
            DB::table('fabricantes')->insert($fabricante);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fabricantes');
    }
};