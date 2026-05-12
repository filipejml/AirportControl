{{-- resources/views/relatorios/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Criar Relatório')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📝 Criar Novo Relatório</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.relatorios.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Relatório *</label>
                            <input type="text" 
                                   class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome') }}" 
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
                                      rows="3">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="visivel_usuario" 
                                   name="visivel_usuario"
                                   {{ old('visivel_usuario') ? 'checked' : '' }}>
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
                                Criar Relatório
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection