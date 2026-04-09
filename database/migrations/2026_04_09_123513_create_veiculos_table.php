<?php
// database/migrations/2026_04_08_000002_create_veiculos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposito_id')->constrained('depositos')->cascadeOnDelete();
            $table->string('placa')->unique();
            $table->string('modelo');
            $table->string('marca');
            $table->integer('ano');
            $table->string('cor')->nullable();
            $table->enum('tipo', ['carro', 'caminhao', 'onibus', 'van', 'utilitario', 'outro'])->default('carro');
            $table->enum('status', ['disponivel', 'em_uso', 'manutencao', 'inativo'])->default('disponivel');
            $table->integer('quilometragem')->default(0);
            $table->integer('capacidade_passageiros')->nullable();
            $table->decimal('carga_maxima', 10, 2)->nullable()->comment('Carga máxima em kg');
            $table->date('data_aquisicao')->nullable();
            $table->date('ultima_manutencao')->nullable();
            $table->date('proxima_manutencao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};