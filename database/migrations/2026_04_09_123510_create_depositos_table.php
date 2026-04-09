<?php
// database/migrations/2026_04_08_000001_create_depositos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depositos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aeroporto_id')->constrained('aeroportos')->cascadeOnDelete();
            $table->string('nome');
            $table->string('codigo')->unique();
            $table->string('localizacao')->nullable();
            $table->decimal('area_total', 10, 2)->nullable()->comment('Área total em m²');
            $table->integer('capacidade_maxima')->nullable()->comment('Capacidade máxima de veículos');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depositos');
    }
};