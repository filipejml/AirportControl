@extends('layouts.app')

@section('title', 'Relatório - Desempenho das Companhias')

@section('content')
    @include('relatorios.partials.desempenho-companhias', ['modoAdmin' => true])
@endsection
