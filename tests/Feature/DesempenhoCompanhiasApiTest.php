<?php

namespace Tests\Feature;

use App\Models\Aeronave;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\User;
use App\Models\Voo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DesempenhoCompanhiasApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('aeroportos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_aeroporto');
            $table->timestamps();
        });
        Schema::create('companhias_aereas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codigo');
            $table->timestamps();
        });
        Schema::create('aeronaves', function (Blueprint $table) {
            $table->id();
            $table->string('modelo');
            $table->integer('capacidade');
            $table->string('porte')->nullable();
            $table->unsignedBigInteger('fabricante_id')->nullable();
            $table->timestamps();
        });
        Schema::create('voos', function (Blueprint $table) {
            $table->id();
            $table->string('id_voo')->unique();
            $table->unsignedBigInteger('aeroporto_id');
            $table->unsignedBigInteger('companhia_aerea_id');
            $table->unsignedBigInteger('aeronave_id');
            $table->string('tipo_voo');
            $table->string('tipo_aeronave')->nullable();
            $table->integer('qtd_voos');
            $table->string('horario_voo');
            $table->integer('qtd_passageiros');
            $table->integer('nota_obj')->nullable();
            $table->integer('nota_pontualidade')->nullable();
            $table->integer('nota_servicos')->nullable();
            $table->integer('nota_patio')->nullable();
            $table->decimal('media_notas', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('voos');
        Schema::dropIfExists('aeronaves');
        Schema::dropIfExists('companhias_aereas');
        Schema::dropIfExists('aeroportos');

        parent::tearDown();
    }

    public function test_api_returns_weighted_company_performance_metrics(): void
    {
        $aeroporto = Aeroporto::create(['nome_aeroporto' => 'Aeroporto Central']);
        $companhia = CompanhiaAerea::create(['nome' => 'Companhia Azul', 'codigo' => 'AZ']);
        $aeronave = Aeronave::create(['modelo' => 'A100', 'capacidade' => 100]);

        Voo::create([
            'id_voo' => 'AZ-1000',
            'aeroporto_id' => $aeroporto->id,
            'companhia_aerea_id' => $companhia->id,
            'aeronave_id' => $aeronave->id,
            'tipo_voo' => 'Regular',
            'tipo_aeronave' => 'PC',
            'qtd_voos' => 4,
            'horario_voo' => 'AM',
            'qtd_passageiros' => 80,
            'nota_obj' => 8,
            'nota_pontualidade' => 6,
            'nota_servicos' => 10,
            'nota_patio' => 8,
            'created_at' => now(),
        ]);

        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $this->actingAs($user)
            ->getJson(route('api.relatorios.desempenho-companhias'))
            ->assertOk()
            ->assertJsonPath('data.0.nome', 'Companhia Azul')
            ->assertJsonPath('data.0.total_voos', 4)
            ->assertJsonPath('data.0.total_passageiros', 320)
            ->assertJsonPath('data.0.media_passageiros_por_voo', 80)
            ->assertJsonPath('data.0.total_aeroportos', 1)
            ->assertJsonPath('data.0.total_aeronaves', 1)
            ->assertJsonPath('data.0.voos_regulares', 4)
            ->assertJsonPath('data.0.voos_charter', 0)
            ->assertJsonPath('data.0.media_geral', 8)
            ->assertJsonPath('totais.total_companhias', 1)
            ->assertJsonPath('totais.total_voos', 4);
    }
}
