{{-- resources/views/voos/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detalhes do Voo</h1>
        <div class="space-x-2">
            <a href="{{ route('voos.edit', $voo) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Editar
            </a>
            <a href="{{ route('voos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">ID do Voo</h3>
                <p class="mt-1 text-lg">{{ $voo->id_voo }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Aeroporto</h3>
                <p class="mt-1 text-lg">{{ $voo->aeroporto->nome_aeroporto }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Companhia Aérea</h3>
                <p class="mt-1 text-lg">{{ $voo->companhiaAerea->nome }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Aeronave</h3>
                <p class="mt-1 text-lg">{{ $voo->aeronave->modelo }} ({{ $voo->aeronave->fabricante->nome ?? 'N/A' }})</p>
                <p class="text-sm text-gray-600">Capacidade: {{ $voo->aeronave->capacidade }} passageiros | Porte: {{ $voo->aeronave->porte }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Tipo de Voo</h3>
                <p class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $voo->tipo_voo == 'Regular' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $voo->tipo_voo }}
                    </span>
                </p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Quantidade de Voos</h3>
                <p class="mt-1 text-lg">{{ $voo->qtd_voos }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Horário do Voo</h3>
                <p class="mt-1 text-lg">{{ $voo->horario_voo }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Passageiros por Voo</h3>
                <p class="mt-1 text-lg">{{ number_format($voo->qtd_passageiros, 0, ',', '.') }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Total de Passageiros</h3>
                <p class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($voo->total_passageiros, 0, ',', '.') }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Data de Cadastro</h3>
                <p class="mt-1 text-lg">{{ $voo->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

    @if($voo->nota_obj || $voo->nota_pontualidade || $voo->nota_servicos || $voo->nota_patio)
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Notas</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if($voo->nota_obj)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Objetivo</h4>
                <p class="mt-1 text-2xl font-bold">{{ $voo->nota_obj_letra }}</p>
                <p class="text-xs text-gray-500">({{ $voo->nota_obj }} pontos)</p>
            </div>
            @endif

            @if($voo->nota_pontualidade)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Pontualidade</h4>
                <p class="mt-1 text-2xl font-bold">{{ $voo->nota_pontualidade_letra }}</p>
                <p class="text-xs text-gray-500">({{ $voo->nota_pontualidade }} pontos)</p>
            </div>
            @endif

            @if($voo->nota_servicos)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Serviços</h4>
                <p class="mt-1 text-2xl font-bold">{{ $voo->nota_servicos_letra }}</p>
                <p class="text-xs text-gray-500">({{ $voo->nota_servicos }} pontos)</p>
            </div>
            @endif

            @if($voo->nota_patio)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Pátio</h4>
                <p class="mt-1 text-2xl font-bold">{{ $voo->nota_patio_letra }}</p>
                <p class="text-xs text-gray-500">({{ $voo->nota_patio }} pontos)</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection