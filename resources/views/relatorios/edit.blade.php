@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        
        <h3 class="mb-4">
            ✏️ Editar Relatório
        </h3>

        <form method="POST" action="{{ route('relatorios.admin.update', $relatorio->id) }}">
            @csrf
            @method('PUT')

            <!-- Nome -->
            <div class="mb-3">
                <label class="form-label">Nome do Relatório</label>
                <input 
                    type="text" 
                    name="nome" 
                    class="form-control"
                    value="{{ $relatorio->nome }}"
                    required
                >
            </div>

            <!-- Descrição -->
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea 
                    name="descricao" 
                    class="form-control"
                    rows="3"
                >{{ $relatorio->descricao }}</textarea>
            </div>

            <!-- 🔥 VISIBILIDADE -->
            <div class="form-check form-switch mb-4">
                <input 
                    type="checkbox" 
                    name="visivel_usuario" 
                    class="form-check-input"
                    value="1"
                    {{ $relatorio->visivel_usuario ? 'checked' : '' }}
                >
                <label class="form-check-label">
                    Disponível para usuários comuns
                </label>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('relatorios') }}" class="btn btn-secondary">
                    ← Voltar
                </a>

                <button class="btn btn-primary">
                    💾 Atualizar
                </button>
            </div>

        </form>
    </div>
</div>
@endsection