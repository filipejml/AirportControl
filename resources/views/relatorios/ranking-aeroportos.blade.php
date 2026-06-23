@extends('layouts.app')

@section('title', 'Ranking de Aeroportos')

@section('content')
    @include('relatorios.partials.ranking-aeroportos', ['modoAdmin' => false])
@endsection
