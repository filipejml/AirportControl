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

class RankingAeroportosApiTest extends TestCase
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

    public function test_api_ranks_airports_by_selected_metric(): void
    {
        $aeroportoA = Aeroporto::create(['nome_aeroporto' => 'Aeroporto A']);
        $aeroportoB = Aeroporto::create(['nome_aeroporto' => 'Aeroporto B']);
        $companhia = CompanhiaAerea::create(['nome' => 'Companhia Teste', 'codigo' => 'CT']);
        $aeronave = Aeronave::create(['modelo' => 'Modelo Teste', 'capacidade' => 100]);

        $this->criarVoo('CT-1000', $aeroportoA->id, $companhia->id, $aeronave->id, 2, 100);
        $this->criarVoo('CT-2000', $aeroportoB->id, $companhia->id, $aeronave->id, 4, 50);

        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $this->actingAs($user)
            ->getJson(route('api.relatorios.ranking-aeroportos', [
                'ordenacao' => 'media_passageiros_por_voo',
            ]))
            ->assertOk()
            ->assertJsonPath('data.0.nome', 'Aeroporto A')
            ->assertJsonPath('data.0.posicao', 1)
            ->assertJsonPath('data.0.media_passageiros_por_voo', 100)
            ->assertJsonPath('data.1.nome', 'Aeroporto B')
            ->assertJsonPath('data.1.posicao', 2)
            ->assertJsonPath('totais.total_aeroportos', 2)
            ->assertJsonPath('totais.total_voos', 6)
            ->assertJsonPath('totais.total_passageiros', 400)
            ->assertJsonPath('totais.lider.nome', 'Aeroporto A');
    }

    public function test_invalid_ordering_is_rejected(): void
    {
        $user = new User(['tipo' => 1]);
        $user->id = 1;

        $this->actingAs($user)
            ->getJson(route('api.relatorios.ranking-aeroportos', [
                'ordenacao' => 'invalida',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ordenacao');
    }

    private function criarVoo(
        string $idVoo,
        int $aeroportoId,
        int $companhiaId,
        int $aeronaveId,
        int $quantidade,
        int $passageiros
    ): void {
        Voo::create([
            'id_voo' => $idVoo,
            'aeroporto_id' => $aeroportoId,
            'companhia_aerea_id' => $companhiaId,
            'aeronave_id' => $aeronaveId,
            'tipo_voo' => 'Regular',
            'tipo_aeronave' => 'PC',
            'qtd_voos' => $quantidade,
            'horario_voo' => 'AM',
            'qtd_passageiros' => $passageiros,
            'nota_obj' => 8,
            'nota_pontualidade' => 8,
            'nota_servicos' => 8,
            'nota_patio' => 8,
            'created_at' => now(),
        ]);
    }
}
