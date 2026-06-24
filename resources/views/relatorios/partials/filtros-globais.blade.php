@php
    $ids = array_merge([
        'periodo' => null,
        'aeroporto' => null,
        'companhia' => null,
        'aeronave' => null,
    ], $ids ?? []);
@endphp

@if($ids['periodo'])
    <div class="col-md-3">
        <label for="{{ $ids['periodo'] }}" class="form-label fw-semibold">Período</label>
        <select id="{{ $ids['periodo'] }}" class="form-select">
            <option value="">Todos os períodos</option>
            <option value="hoje">Hoje</option>
            <option value="semana">Esta semana</option>
            <option value="mes">Este mês</option>
            <option value="ano">Este ano</option>
        </select>
    </div>
@endif

@if($ids['aeroporto'])
    <div class="col-md-3">
        <label for="{{ $ids['aeroporto'] }}" class="form-label fw-semibold">Aeroporto</label>
        <select id="{{ $ids['aeroporto'] }}" class="form-select">
            <option value="">Todos os aeroportos</option>
            @foreach($aeroportos ?? [] as $aeroporto)
                <option value="{{ $aeroporto->id }}">{{ $aeroporto->nome_aeroporto }}</option>
            @endforeach
        </select>
    </div>
@endif

@if($ids['companhia'])
    <div class="col-md-3">
        <label for="{{ $ids['companhia'] }}" class="form-label fw-semibold">Companhia</label>
        <select id="{{ $ids['companhia'] }}" class="form-select">
            <option value="">Todas as companhias</option>
            @foreach($companhias ?? [] as $companhia)
                <option value="{{ $companhia->id }}">
                    {{ $companhia->nome }}{{ $companhia->codigo ? " ({$companhia->codigo})" : '' }}
                </option>
            @endforeach
        </select>
    </div>
@endif

@if($ids['aeronave'])
    <div class="col-md-3">
        <label for="{{ $ids['aeronave'] }}" class="form-label fw-semibold">Aeronave</label>
        <select id="{{ $ids['aeronave'] }}" class="form-select">
            <option value="">Todas as aeronaves</option>
            @foreach($aeronaves ?? [] as $aeronave)
                <option value="{{ $aeronave->id }}">
                    {{ $aeronave->modelo }}{{ $aeronave->fabricante ? " ({$aeronave->fabricante->nome})" : '' }}
                </option>
            @endforeach
        </select>
    </div>
@endif
