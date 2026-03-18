@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Relatórios</h3>

    <div class="row">
        @foreach($relatorios as $relatorio)
            <div class="col-md-4 mb-3">
                <div class="card p-3 shadow-sm">
                    <h5>{{ $relatorio->nome }}</h5>
                    <p class="text-muted">{{ $relatorio->descricao }}</p>

                    <a href="#" class="btn btn-primary btn-sm">
                        Acessar
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection