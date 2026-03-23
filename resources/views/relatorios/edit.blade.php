{{-- resources/views/relatorios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Relatório')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">✏️ Editar Relatório</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.relatorios.update', $relatorio) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Relatório *</label>
                            <input type="text" 
                                   class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome', $relatorio->nome) }}" 
                                   required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="3">{{ old('descricao', $relatorio->descricao) }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="visivel_usuario" 
                                   name="visivel_usuario"
                                   {{ old('visivel_usuario', $relatorio->visivel_usuario) ? 'checked' : '' }}>
                            <label class="form-check-label" for="visivel_usuario">
                                Visível para usuários comuns
                            </label>
                            <div class="form-text">
                                Se marcado, usuários comuns poderão visualizar este relatório.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.relatorios.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Atualizar Relatório
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection