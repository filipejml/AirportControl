@extends('layouts.app')

@section('title', 'Registros')

@section('content')

<div class="mb-4">
    <h3 class="fw-bold">⚙️ Painel de Registros</h3>
    <p class="text-muted">Gerencie os dados do sistema</p>
</div>

<div class="row g-4">

    <!-- Aeronaves -->
    <div class="col-md-4">
        <div class="card shadow-sm p-3 h-100">
            <h5>✈️ Aeronaves</h5>
            <p class="text-muted">Cadastrar e gerenciar aeronaves</p>

            <a href="{{ route('aeronaves.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                Ver lista
            </a>

            <a href="{{ route('aeronaves.create') }}" class="btn btn-primary btn-sm">
                + Nova Aeronave
            </a>
        </div>
    </div>

    <!-- Fabricantes -->
    <div class="col-md-4">
        <div class="card shadow-sm p-3 h-100">
            <h5>🏭 Fabricantes</h5>
            <p class="text-muted">Gerenciar fabricantes de aeronaves</p>

            <a href="{{ route('fabricantes.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                Ver lista
            </a>

            <a href="{{ route('fabricantes.create') }}" class="btn btn-primary btn-sm">
                + Novo Fabricante
            </a>
        </div>
    </div>

    <!-- Companhias -->
    <div class="col-md-4">
        <div class="card shadow-sm p-3 h-100">
            <h5>🏢 Companhias Aéreas</h5>
            <p class="text-muted">Gerenciar companhias e suas aeronaves</p>

            <a href="{{ route('companhias.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                Ver lista
            </a>

            <a href="{{ route('companhias.create') }}" class="btn btn-primary btn-sm">
                + Nova Companhia
            </a>
        </div>
    </div>

    <!-- Aeroportos -->
    <div class="col-md-4">
        <div class="card shadow-sm p-3 h-100">
            <h5>🛫 Aeroportos</h5>
            <p class="text-muted">Gerenciar aeroportos e companhias</p>

            <a href="{{ route('aeroportos.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                Ver lista
            </a>

            <a href="{{ route('aeroportos.create') }}" class="btn btn-primary btn-sm">
                + Novo Aeroporto
            </a>
        </div>
    </div>

</div>

@endsection