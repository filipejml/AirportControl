@extends('layouts.app')

@section('title', 'Ocupação dos Voos')

@section('content')
    @include('relatorios.partials.ocupacao-voos', ['modoAdmin' => false])
@endsection
