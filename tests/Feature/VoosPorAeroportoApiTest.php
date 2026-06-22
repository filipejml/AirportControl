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

class VoosPorAeroportoApiTest extends TestCase
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

    public function test_period_filter_only_aggregates_flights_from_the_requested_period(): void
    {
        $aeroporto = Aeroporto::create(['nome_aeroporto' => 'Aeroporto Teste']);
        $companhia = CompanhiaAerea::create(['nome' => 'Companhia Teste', 'codigo' => 'CT']);
        $aeronave = Aeronave::create(['modelo' => 'Modelo Teste', 'capacidade' => 100]);

        Voo::create([
            'id_voo' => 'CT-1000',
            'aeroporto_id' => $aeroporto->id,
            'companhia_aerea_id' => $companhia->id,
            'aeronave_id' => $aeronave->id,
            'tipo_voo' => 'Regular',
            'tipo_aeronave' => 'PC',
            'qtd_voos' => 2,
            'horario_voo' => 'AM',
            'qtd_passageiros' => 100,
            'nota_obj' => 10,
            'nota_pontualidade' => 8,
            'nota_servicos' => 6,
            'nota_patio' => 4,
            'created_at' => now(),
        ]);

        Voo::create([
            'id_voo' => 'CT-2000',
            'aeroporto_id' => $aeroporto->id,
            'companhia_aerea_id' => $companhia->id,
            'aeronave_id' => $aeronave->id,
            'tipo_voo' => 'Charter',
            'tipo_aeronave' => 'PC',
            'qtd_voos' => 5,
            'horario_voo' => 'PM',
            'qtd_passageiros' => 100,
            'nota_obj' => 2,
            'nota_pontualidade' => 2,
            'nota_servicos' => 2,
            'nota_patio' => 2,
            'created_at' => now()->subYear(),
        ]);

        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.relatorios.voos-por-aeroporto', ['periodo' => 'hoje']));

        $response
            ->assertOk()
            ->assertJsonPath('periodo', 'hoje')
            ->assertJsonPath('totais.total_voos', 2)
            ->assertJsonPath('totais.total_passageiros', 200)
            ->assertJsonPath('data.0.voos_regulares', 2)
            ->assertJsonPath('data.0.voos_charter', 0)
            ->assertJsonPath('data.0.voos_por_horario.AM', 2)
            ->assertJsonPath('data.0.voos_por_horario.PM', 0)
            ->assertJsonPath('data.0.companhias.0.nome', 'Companhia Teste')
            ->assertJsonPath('data.0.companhias.0.total_voos', 2)
            ->assertJsonPath('data.0.media_geral', 7);
    }

    public function test_invalid_period_is_rejected(): void
    {
        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $this
            ->actingAs($user)
            ->getJson(route('api.relatorios.voos-por-aeroporto', ['periodo' => 'invalido']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('periodo');
    }
}
