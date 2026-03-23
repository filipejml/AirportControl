@extends('layouts.app')

@section('title', 'Home - Airport Manager')

@section('styles')
    <style>
        body {
            background-color: #f5f7fa;
        }

        .card-dashboard {
            border-radius: 12px;
            transition: 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        
        .card-dashboard .btn {
            margin-top: auto;
        }
    </style>
@endsection

@section('content')
    <!-- Cabeçalho -->
    <div class="mb-4">
        <h3 class="fw-bold">
            Bem-vindo, {{ auth()->user()->name }}
        </h3>
        <p class="text-muted">
            @if(auth()->user()->tipo == 0)
                <span class="badge bg-danger">Administrador</span>
            @else
                <span class="badge bg-secondary">Usuário comum</span>
            @endif
        </p>
    </div>

    <!-- Cards do sistema -->
    <div class="row g-4">

        <!-- Card Cadastro de Voos -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-plus-circle fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Cadastro de Voos</h5>
                </div>
                <p class="text-muted flex-grow-1">Cadastrar novos voos no sistema</p>
                <a href="{{ route('voos.create') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- Card Lista de Voos -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-list-ul fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Lista de Voos</h5>
                </div>
                <p class="text-muted flex-grow-1">Visualizar todos os voos cadastrados</p>
                <a href="{{ route('voos.index') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- Card Companhias Aéreas (visível para todos) -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-building fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Companhias Aéreas</h5>
                </div>
                <p class="text-muted flex-grow-1">Visualizar informações gerais das companhias</p>
                <a href="{{ route('companhias.informacoes') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- Card Aeronaves (visível para todos) -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-airplane fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Aeronaves</h5>
                </div>
                <p class="text-muted flex-grow-1">Visualizar informações gerais das aeronaves</p>
                <a href="{{ route('aeronaves.informacoes') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- Card Aeroportos (visível para todos) -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-geo-alt fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Aeroportos</h5>
                </div>
                <p class="text-muted flex-grow-1">Visualizar informações gerais dos aeroportos</p>
                <a href="{{ route('aeroportos.informacoes') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- Card Dashboard -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-bar-chart fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Dashboard</h5>
                </div>
                <p class="text-muted flex-grow-1">Análises e estatísticas</p>
                <a href="#" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- Card Relatórios -->
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-file-text fs-1 text-primary me-3"></i>
                    <h5 class="mb-0">Relatórios</h5>
                </div>
                <p class="text-muted flex-grow-1">Gerar relatórios do sistema</p>
                <a href="{{ route('relatorios') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>

        <!-- SOMENTE ADMIN - Registros -->
        @if(auth()->user()->tipo == 0)
        <div class="col-md-4">
            <div class="card card-dashboard shadow-sm p-3 border-danger">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shield-lock fs-1 text-danger me-3"></i>
                    <h5 class="mb-0 text-danger">Registros</h5>
                </div>
                <p class="text-muted flex-grow-1">Logs e atividades do sistema</p>
                <a href="{{ route('registros') }}" class="btn btn-danger btn-sm w-100">
                    <i class="bi bi-arrow-right me-1"></i>Acessar
                </a>
            </div>
        </div>
        @endif

    </div>
@endsection