<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RelatorioFiltrosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'periodo' => ['nullable', 'in:hoje,semana,mes,ano'],
            'aeroporto_id' => ['nullable', 'integer', 'exists:aeroportos,id'],
            'companhia_id' => ['nullable', 'integer', 'exists:companhias_aereas,id'],
            'aeronave_id' => ['nullable', 'integer', 'exists:aeronaves,id'],
        ];

        return array_merge($rules, match ($this->route()?->getName()) {
            'api.relatorios.movimentacao-por-periodo' => [
                'agrupamento' => ['nullable', 'in:dia,semana,mes,ano'],
                'data_inicio' => ['nullable', 'date'],
                'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            ],
            'api.relatorios.ranking-aeroportos' => [
                'ordenacao' => [
                    'nullable',
                    'in:total_voos,total_passageiros,media_passageiros_por_voo,total_companhias,media_geral',
                ],
            ],
            'api.relatorios.ocupacao-voos' => [
                'faixa' => ['nullable', 'in:baixa,media,alta,lotado'],
            ],
            default => [],
        });
    }

    public function filtros(): array
    {
        return $this->validated();
    }
}
