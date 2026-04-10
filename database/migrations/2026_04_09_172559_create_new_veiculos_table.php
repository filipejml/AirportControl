<?php

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
            $table->string('codigo')->unique();
            $table->integer('quantidade')->default(1);
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
            $table->enum('status', ['disponivel', 'indisponivel'])->default('disponivel');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};