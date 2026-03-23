<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            padding: 20px;
            color: #333;
        }
        
        /* Cabeçalho do relatório */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0d5c8b;
        }
        
        .header h1 {
            color: #0d5c8b;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 11px;
        }
        
        .header .data-geracao {
            color: #999;
            font-size: 9px;
            margin-top: 5px;
        }
        
        /* Cards de estatísticas */
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            flex: 1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            border-left: 4px solid #0d5c8b;
        }
        
        .stat-card .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #0d5c8b;
            margin-top: 5px;
        }
        
        .stat-card .small {
            font-size: 8px;
            color: #999;
            margin-top: 3px;
        }
        
        /* Filtros aplicados */
        .filters {
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 9px;
        }
        
        .filters strong {
            color: #0d5c8b;
        }
        
        /* Tabela */
        .table-container {
            margin-top: 15px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        th {
            background: #0d5c8b;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #1a6a9a;
        }
        
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f0f0f0;
        }
        
        /* Classes de notas */
        .nota-boa {
            color: #28a745;
            font-weight: bold;
        }
        
        .nota-regular {
            color: #ffc107;
            font-weight: bold;
        }
        
        .nota-ruim {
            color: #dc3545;
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        
        /* Rodapé */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #999;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Alinhamentos */
        .text-left {
            text-align: left;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>{{ $empresa }}</h1>
        <div class="subtitle">{{ $titulo }}</div>
        <div class="data-geracao">Gerado em: {{ $data_geracao }}</div>
    </div>
    
    <!-- Cards de Estatísticas -->
    <div class="stats">
        <div class="stat-card">
            <div class="label">Total de Voos</div>
            <div class="value">{{ $estatisticas['total_voos'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Passageiros</div>
            <div class="value">{{ number_format($estatisticas['total_passageiros'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Média Pax/Voo</div>
            <div class="value">{{ $estatisticas['media_pax_voo'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Média Geral Notas</div>
            <div class="value">{{ $estatisticas['media_geral_notas'] ? number_format($estatisticas['media_geral_notas'], 1) : 'N/A' }}</div>
            <div class="small">{{ $estatisticas['voos_com_notas'] }} voos avaliados</div>
        </div>
    </div>
    
    <!-- Filtros Aplicados -->
    <div class="filters">
        <strong>Filtros aplicados:</strong> {{ $estatisticas['filtros_aplicados'] }}
    </div>
    
    <!-- Tabela de Voos -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nº Voo</th>
                    <th>Aeroporto</th>
                    <th>Companhia</th>
                    <th>Modelo</th>
                    <th>Tipo</th>
                    <th>Av.</th>
                    <th>Qtde</th>
                    <th>Horário</th>
                    <th>Passag.</th>
                    <th>Obj.</th>
                    <th>Pont.</th>
                    <th>Serv.</th>
                    <th>Pátio</th>
                    <th>Média</th>
                </tr>
            </thead>
            <tbody>
                @forelse($voos as $index => $voo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $voo->id_voo }}</strong>
                        <br>
                        <small>{{ $voo->created_at ? $voo->created_at->format('d/m/Y') : '' }}</small>
                    </td>
                    <td class="text-left">{{ $voo->aeroporto->codigo_icao ?? $voo->aeroporto->nome_aeroporto }}</td>
                    <td class="text-left">{{ $voo->companhiaAerea->codigo ?? $voo->companhiaAerea->nome }}</td>
                    <td class="text-left">{{ $voo->aeronave->modelo }}</td>
                    <td>
                        @if($voo->tipo_voo == 'Regular')
                            <span class="badge badge-success">R</span>
                        @else
                            <span class="badge badge-info">C</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $porteTexto = ['PC' => 'P', 'MC' => 'M', 'LC' => 'G'];
                            $porteCor = ['PC' => 'badge-secondary', 'MC' => 'badge-info', 'LC' => 'badge-warning'];
                        @endphp
                        <span class="badge {{ $porteCor[$voo->tipo_aeronave] ?? 'badge-secondary' }}">
                            {{ $porteTexto[$voo->tipo_aeronave] ?? $voo->tipo_aeronave }}
                        </span>
                    </td>
                    <td>{{ $voo->qtd_voos }}</td>
                    <td>
                        <span class="badge badge-secondary">{{ $voo->horario_voo }}</span>
                    </td>
                    <td>{{ number_format($voo->total_passageiros, 0, ',', '.') }}</td>
                    <td class="{{ $voo->nota_obj ? ($voo->nota_obj >= 8 ? 'nota-boa' : ($voo->nota_obj >= 6 ? 'nota-regular' : 'nota-ruim')) : '' }}">
                        {{ $voo->nota_obj ?? '-' }}
                    </td>
                    <td class="{{ $voo->nota_pontualidade ? ($voo->nota_pontualidade >= 8 ? 'nota-boa' : ($voo->nota_pontualidade >= 6 ? 'nota-regular' : 'nota-ruim')) : '' }}">
                        {{ $voo->nota_pontualidade ?? '-' }}
                    </td>
                    <td class="{{ $voo->nota_servicos ? ($voo->nota_servicos >= 8 ? 'nota-boa' : ($voo->nota_servicos >= 6 ? 'nota-regular' : 'nota-ruim')) : '' }}">
                        {{ $voo->nota_servicos ?? '-' }}
                    </td>
                    <td class="{{ $voo->nota_patio ? ($voo->nota_patio >= 8 ? 'nota-boa' : ($voo->nota_patio >= 6 ? 'nota-regular' : 'nota-ruim')) : '' }}">
                        {{ $voo->nota_patio ?? '-' }}
                    </td>
                    <td>
                        @if($voo->media_notas)
                            @php
                                $mediaCor = match(true) {
                                    $voo->media_notas >= 9 => 'nota-boa',
                                    $voo->media_notas >= 7 => 'nota-regular',
                                    default => 'nota-ruim'
                                };
                            @endphp
                            <span class="{{ $mediaCor }}">{{ number_format($voo->media_notas, 1) }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="text-center">Nenhum voo encontrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo sistema Airport Manager</p>
        <p>Página {PAGE_NUM} de {PAGE_COUNT}</p>
    </div>
</body>
</html>