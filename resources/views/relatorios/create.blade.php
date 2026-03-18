@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Criar Relatório</h3>

    <form method="POST" action="{{ route('relatorios.admin.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control"></textarea>
        </div>

        <!-- 🔥 AQUI -->
        <div class="form-check mb-3">
            <input type="checkbox" name="visivel_usuario" class="form-check-input" value="1">
            <label class="form-check-label">
                Visível para usuários comuns
            </label>
        </div>

        <button class="btn btn-success">Salvar</button>
    </form>
</div>
@endsection