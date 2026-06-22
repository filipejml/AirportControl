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

class OcupacaoVoosApiTest extends TestCase
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

    public function test_api_calculates_weighted_occupancy_from_aircraft_capacity(): void
    {
        $aeroporto = Aeroporto::create(['nome_aeroporto' => 'Aeroporto Central']);
        $companhia = CompanhiaAerea::create(['nome' => 'Companhia Teste', 'codigo' => 'CT']);
        $aeronave = Aeronave::create(['modelo' => 'A100', 'capacidade' => 100]);

        Voo::create([
            'id_voo' => 'CT-1000',
            'aeroporto_id' => $aeroporto->id,
            'companhia_aerea_id' => $companhia->id,
            'aeronave_id' => $aeronave->id,
            'tipo_voo' => 'Regular',
            'tipo_aeronave' => 'PC',
            'qtd_voos' => 4,
            'horario_voo' => 'AM',
            'qtd_passageiros' => 75,
            'nota_obj' => 8,
            'nota_pontualidade' => 8,
            'nota_servicos' => 8,
            'nota_patio' => 8,
            'created_at' => now(),
        ]);

        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $this->actingAs($user)
            ->getJson(route('api.relatorios.ocupacao-voos'))
            ->assertOk()
            ->assertJsonPath('data.0.id_voo', 'CT-1000')
            ->assertJsonPath('data.0.assentos_ofertados', 400)
            ->assertJsonPath('data.0.total_passageiros', 300)
            ->assertJsonPath('data.0.taxa_ocupacao', 75)
            ->assertJsonPath('data.0.faixa_ocupacao', 'alta')
            ->assertJsonPath('totais.total_voos', 4)
            ->assertJsonPath('totais.assentos_ofertados', 400)
            ->assertJsonPath('totais.taxa_ocupacao_geral', 75)
            ->assertJsonPath('distribuicao.alta', 1);
    }

    public function test_invalid_occupancy_range_is_rejected(): void
    {
        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $this->actingAs($user)
            ->getJson(route('api.relatorios.ocupacao-voos', ['faixa' => 'invalida']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('faixa');
    }
}
