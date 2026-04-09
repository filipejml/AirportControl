{{-- resources/views/admin/aeroportos/depositos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Depósitos - ' . $aeroporto->nome_aeroporto)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🏢 Depósitos - {{ $aeroporto->nome_aeroporto }}</h2>
            <p class="text-muted">Gerencie os depósitos de veículos do aeroporto</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.create', $aeroporto) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Depósito
            </a>
            <a href="{{ route('aeroportos.show', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @forelse($depositos as $deposito)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $deposito->nome }}</h5>
                            <span class="badge bg-{{ $deposito->status === 'ativo' ? 'success' : ($deposito->status === 'manutencao' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($deposito->status) }}
                            </span>
                        </div>
                        <small class="text-muted">Código: {{ $deposito->codigo }}</small>
                    </div>
                    <div class="card-body">
                        <p><i class="bi bi-geo-alt"></i> {{ $deposito->localizacao ?? 'Localização não informada' }}</p>
                        <p><i class="bi bi-box"></i> Área: {{ $deposito->area_total ? number_format($deposito->area_total, 2) . ' m²' : 'N/I' }}</p>
                        
                        <div class="progress mb-2" style="height: 8px;">
                            @php
                                $percentual = $deposito->capacidade_maxima ? min(100, ($deposito->veiculos_count / $deposito->capacidade_maxima) * 100) : 0;
                                $barClass = $percentual >= 90 ? 'bg-danger' : ($percentual >= 70 ? 'bg-warning' : 'bg-success');
                            @endphp
                            <div class="progress-bar {{ $barClass }}" style="width: {{ $percentual }}%"></div>
                        </div>
                        
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <h3 class="mb-0">{{ $deposito->veiculos_count }}</h3>
                                <small class="text-muted">Veículos</small>
                            </div>
                            <div class="col-6">
                                <h3 class="mb-0">{{ $deposito->capacidade_maxima ?: '∞' }}</h3>
                                <small class="text-muted">Capacidade</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="btn-group w-100">
                            <a href="{{ route('aeroportos.depositos.show', [$aeroporto, $deposito]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ route('aeroportos.depositos.edit', [$aeroporto, $deposito]) }}" 
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form action="{{ route('aeroportos.depositos.destroy', [$aeroporto, $deposito]) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Tem certeza?')">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle fs-1"></i>
                    <h5>Nenhum depósito cadastrado</h5>
                    <a href="{{ route('aeroportos.depositos.create', $aeroporto) }}" class="btn btn-primary mt-2">
                        Cadastrar Primeiro Depósito
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection