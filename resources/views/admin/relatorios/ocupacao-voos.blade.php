@extends('layouts.app')

@section('title', 'Relatório - Ocupação dos Voos')

@section('content')
    @include('relatorios.partials.ocupacao-voos', ['modoAdmin' => true])
@endsection
