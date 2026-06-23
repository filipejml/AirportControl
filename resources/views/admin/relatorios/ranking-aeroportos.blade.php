@extends('layouts.app')

@section('title', 'Relatório - Ranking de Aeroportos')

@section('content')
    @include('relatorios.partials.ranking-aeroportos', ['modoAdmin' => true])
@endsection
