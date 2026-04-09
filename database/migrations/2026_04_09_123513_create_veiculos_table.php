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
            $table->string('placa')->nullable()->unique();
            $table->enum('status', ['disponivel', 'em_uso', 'manutencao', 'inativo'])->default('disponivel');
            
            // Campos específicos por tipo de veículo
            $table->integer('capacidade_operacional')->nullable()->comment('Capacidade em kg ou litros ou metros');
            $table->string('unidade_capacidade')->nullable()->comment('kg, litros, m³, unidades');
            $table->integer('horimetro')->default(0)->comment('Horas de operação');
            $table->date('ultima_manutencao')->nullable();
            $table->date('proxima_manutencao')->nullable();
            $table->integer('manutencao_prevista_horas')->nullable()->comment('Manutenção a cada X horas');
            
            // Certificações e licenças
            $table->string('certificado_operacao')->nullable();
            $table->date('validade_certificado')->nullable();
            $table->string('operadores_autorizados')->nullable()->comment('IDs ou nomes dos operadores autorizados');
            
            // Manutenção e histórico
            $table->text('observacoes')->nullable();
            $table->json('historico_manutencoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};