@extends('layouts.app')

@section('title', 'Relatório - Movimentação por Período')

@section('content')
    @include('relatorios.partials.movimentacao-por-periodo', ['modoAdmin' => true])
@endsection
