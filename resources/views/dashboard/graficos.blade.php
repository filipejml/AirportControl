@extends('layouts.app')

@section('title', 'Gráficos - Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="bi bi-graph-up me-2"></i>Gráficos
                    </h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Página de Gráficos em desenvolvimento. Em breve você terá acesso a visualizações gráficas das métricas do sistema.
                    </div>
                    
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart-steps" style="font-size: 5rem; color: #198754;"></i>
                        <h4 class="mt-3 text-muted">Gráficos em Construção</h4>
                        <p class="text-muted">Aguardando implementação das visualizações</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection