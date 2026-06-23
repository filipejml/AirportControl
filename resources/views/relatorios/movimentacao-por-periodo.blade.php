@extends('layouts.app')

@section('title', 'Movimentação por Período')

@section('content')
    @include('relatorios.partials.movimentacao-por-periodo', ['modoAdmin' => false])
@endsection
