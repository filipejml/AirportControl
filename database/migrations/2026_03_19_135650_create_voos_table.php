<?php
// database/migrations/2026_03_19_000001_create_voos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voos', function (Blueprint $table) {
            $table->id();
            $table->string('id_voo')->unique(); // LL-NNNN
            $table->foreignId('aeroporto_id')->constrained('aeroportos');
            $table->foreignId('companhia_aerea_id')->constrained('companhias_aereas');
            $table->foreignId('aeronave_id')->constrained('aeronaves');
            $table->enum('tipo_voo', ['Regular', 'Charter'])->default('Regular');
            $table->integer('qtd_voos');
            $table->enum('horario_voo', ['EAM', 'AM', 'AN', 'PM', 'ALL']);
            $table->integer('qtd_passageiros');
            $table->integer('total_passageiros')->virtualAs('qtd_voos * qtd_passageiros');
            
            // Notas (salvas como números de 0-10)
            $table->integer('nota_obj')->nullable();
            $table->integer('nota_pontualidade')->nullable();
            $table->integer('nota_servicos')->nullable();
            $table->integer('nota_patio')->nullable();
            
            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voos');
    }
};