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
            $table->string('codigo')->unique()->comment('Código de identificação do veículo');
            $table->enum('tipo_veiculo', [
                'esteira_bagagem',
                'caminhao_combustivel',
                'carro_inspecao',
                'carrinho_bagagem',
                'caminhao_pushback',
                'caminhao_escada',
                'caminhao_limpeza',
                'outro'
            ])->default('outro');
            $table->string('modelo')->nullable();
            $table->string('fabricante')->nullable();
            $table->integer('ano_fabricacao')->nullable();
            
            // Capacidade operacional específica por tipo
            $table->decimal('capacidade_operacional', 10, 2)->nullable()->comment('Capacidade em kg, litros, metros ou unidades');
            $table->string('unidade_capacidade')->nullable()->comment('kg, litros, m³, unidades, toneladas');
            
            // Status simplificado (apenas disponível ou não)
            $table->enum('status', ['disponivel', 'indisponivel'])->default('disponivel');
            
            // Observações gerais
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};