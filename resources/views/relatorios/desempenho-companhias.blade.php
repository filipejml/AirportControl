@extends('layouts.app')

@section('title', 'Desempenho das Companhias')

@section('content')
    @include('relatorios.partials.desempenho-companhias', ['modoAdmin' => false])
@endsection
