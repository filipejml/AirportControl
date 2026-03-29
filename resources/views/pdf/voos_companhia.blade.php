<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Voos - {{ $companhia->nome }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #0d6efd;
        }
        
        .header .subtitle {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .company-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .company-info h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }
        
        .company-info p {
            margin: 0;
            font-size: 10px;
            color: #6c757d;
        }
        
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            flex: 1;
            min-width: 100px;
            background: #f8f9fa;
            border-radius: 5px;
            padding: 8px;
            text-align: center;
            border-left: 3px solid;
        }
        
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.info { border-left-color: #0dcaf0; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6c757d;
        }
        
        .ratings {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .ratings h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
        }
        
        .rating-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .rating-label {
            width: 80px;
            font-size: 9px;
        }
        
        .rating-bar {
            flex: 1;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin: 0 8px;
        }
        
        .rating-bar-fill {
            height: 100%;
            border-radius: 4px;
            background-color: #0d6efd;
        }
        
        .rating-value {
            width: 35px;
            text-align: right;
            font-size: 9px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8px;
        }
        
        th {
            background-color: #0d6efd;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
        }
        
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 10px;
        }
        
        .badge-success { background-color: #198754; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #0dcaf0; color: #000; }
        .badge-secondary { background-color: #6c757d; color: white; }
        
        .charts-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .chart-box {
            flex: 1;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .chart-box h5 {
            margin: 0 0 8px 0;
            font-size: 11px;
            text-align: center;
        }
        
        .chart-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .chart-label {
            width: 100px;
            font-size: 9px;
        }
        
        .chart-bar {
            flex: 1;
            height: 12px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .chart-bar-fill {
            height: 100%;
            background-color: #0d6efd;
        }
        
        .chart-value {
            width: 45px;
            text-align: right;
            font-size: 9px;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $companhia->nome }}</h1>
        <div class="subtitle">Relatório de Voos - Gerado em {{ $dataGeracao }}</div>
        @if($companhia->codigo)
            <div class="subtitle">Código: {{ $companhia->codigo }}</div>
        @endif
    </div>
    
    <div class="company-info">
        <h3>Informações da Companhia</h3>
        <p>Aeronaves na frota: {{ number_format($totalAeronaves, 0, ',', '.') }} | 
           Aeroportos operados: {{ number_format($totalAeroportos, 0, ',', '.') }} | 
           Total de voos: {{ number_format($totalVoos, 0, ',', '.') }} | 
           Total de passageiros: {{ number_format($totalPassageiros, 0, ',', '.') }}</p>
    </div>
    
    {{-- Cards de Estatísticas --}}
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-value">{{ number_format($totalVoos, 0, ',', '.') }}</div>
            <div class="stat-label">Voos Realizados</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value">{{ number_format($totalPassageiros, 0, ',', '.') }}</div>
            <div class="stat-label">Passageiros Transportados</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value">{{ number_format($totalAeronaves, 0, ',', '.') }}</div>
            <div class="stat-label">Aeronaves na Frota</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value">{{ number_format($totalAeroportos, 0, ',', '.') }}</div>
            <div class="stat-label">Aeroportos Operados</div>
        </div>
    </div>
    
    {{-- Avaliações --}}
    <div class="ratings">
        <h4>Avaliações (0-10)</h4>
        <div class="rating-row">
            <div class="rating-label">Objetivo:</div>
            <div class="rating-bar"><div class="rating-bar-fill" style="width: {{ ($notaObj / 10) * 100 }}%"></div></div>
            <div class="rating-value">{{ number_format($notaObj, 1) }}</div>
        </div>
        <div class="rating-row">
            <div class="rating-label">Pontualidade:</div>
            <div class="rating-bar"><div class="rating-bar-fill" style="width: {{ ($notaPontualidade / 10) * 100 }}%"></div></div>
            <div class="rating-value">{{ number_format($notaPontualidade, 1) }}</div>
        </div>
        <div class="rating-row">
            <div class="rating-label">Serviços:</div>
            <div class="rating-bar"><div class="rating-bar-fill" style="width: {{ ($notaServicos / 10) * 100 }}%"></div></div>
            <div class="rating-value">{{ number_format($notaServicos, 1) }}</div>
        </div>
        <div class="rating-row">
            <div class="rating-label">Pátio:</div>
            <div class="rating-bar"><div class="rating-bar-fill" style="width: {{ ($notaPatio / 10) * 100 }}%"></div></div>
            <div class="rating-value">{{ number_format($notaPatio, 1) }}</div>
        </div>
        <div class="rating-row mt-2">
            <div class="rating-label fw-bold">Média Geral:</div>
            <div class="rating-bar"><div class="rating-bar-fill" style="background-color: #ffc107; width: {{ ($mediaGeral / 10) * 100 }}%"></div></div>
            <div class="rating-value fw-bold">{{ number_format($mediaGeral, 1) }}</div>
        </div>
    </div>
    
    {{-- Gráficos de Distribuição --}}
    <div class="charts-container">
        {{-- Voos por Aeroporto (Top 5) --}}
        <div class="chart-box">
            <h5>📊 Top 5 Aeroportos (Voos)</h5>
            @foreach($voosPorAeroporto as $aeroporto => $quantidade)
                @php $percentual = ($quantidade / $totalVoos) * 100; @endphp
                <div class="chart-item">
                    <div class="chart-label">{{ $aeroporto }}</div>
                    <div class="chart-bar"><div class="chart-bar-fill" style="width: {{ $percentual }}%"></div></div>
                    <div class="chart-value">{{ number_format($quantidade, 0, ',', '.') }} ({{ number_format($percentual, 1) }}%)</div>
                </div>
            @endforeach
        </div>
        
        {{-- Voos por Horário --}}
        <div class="chart-box">
            <h5>🕐 Distribuição por Horário</h5>
            @php
                $horarioLabels = [
                    'EAM' => 'Madrugada (00-06h)',
                    'AM' => 'Manhã (06-12h)',
                    'AN' => 'Tarde (12-18h)',
                    'PM' => 'Noite (18-00h)',
                    'ALL' => 'Diário'
                ];
            @endphp
            @foreach($voosPorHorario as $horario => $quantidade)
                @php $percentual = $totalVoos > 0 ? ($quantidade / $totalVoos) * 100 : 0; @endphp
                @if($quantidade > 0)
                <div class="chart-item">
                    <div class="chart-label">{{ $horarioLabels[$horario] ?? $horario }}</div>
                    <div class="chart-bar"><div class="chart-bar-fill" style="width: {{ $percentual }}%"></div></div>
                    <div class="chart-value">{{ number_format($quantidade, 0, ',', '.') }} ({{ number_format($percentual, 1) }}%)</div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    
    {{-- Lista Completa de Voos --}}
    <table>
        <thead>
            <tr>
                <th>ID Voo</th>
                <th>Data</th>
                <th>Aeroporto</th>
                <th>Aeronave</th>
                <th>Tipo Voo</th>
                <th>Tipo Aeronave</th>
                <th>Qtd Voos</th>
                <th>Horário</th>
                <th>Passageiros</th>
                <th>Objetivo</th>
                <th>Pontualidade</th>
                <th>Serviços</th>
                <th>Pátio</th>
                <th>Média</th>
            </tr>
        </thead>
        <tbody>
            @foreach($voos as $voo)
                @php
                    $mediaNota = $voo->media_notas ?? (($voo->nota_obj + $voo->nota_pontualidade + $voo->nota_servicos + $voo->nota_patio) / 4);
                    $mediaNota = $mediaNota ?? 0;
                    $badgeClass = $mediaNota >= 8 ? 'badge-success' : ($mediaNota >= 6 ? 'badge-warning' : ($mediaNota >= 4 ? 'badge-info' : 'badge-danger'));
                @endphp
                <tr>
                    <td><strong>{{ $voo->id_voo }}</strong></td>
                    <td>{{ $voo->created_at ? $voo->created_at->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $voo->aeroporto->nome_aeroporto ?? 'N/A' }}</td>
                    <td>{{ $voo->aeronave->modelo ?? 'N/A' }}</td>
                    <td>{{ $voo->tipo_voo }}</td>
                    <td>{{ $voo->tipo_aeronave ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format($voo->qtd_voos, 0, ',', '.') }}</td>
                    <td>{{ $voo->horario_voo }}</td>
                    <td class="text-end">{{ number_format($voo->qtd_passageiros, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $voo->nota_obj ? number_format($voo->nota_obj, 1) : '-' }}</td>
                    <td class="text-center">{{ $voo->nota_pontualidade ? number_format($voo->nota_pontualidade, 1) : '-' }}</td>
                    <td class="text-center">{{ $voo->nota_servicos ? number_format($voo->nota_servicos, 1) : '-' }}</td>
                    <td class="text-center">{{ $voo->nota_patio ? number_format($voo->nota_patio, 1) : '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ number_format($mediaNota, 1) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="6" class="text-end">TOTAIS:</td>
                <td class="text-end">{{ number_format($voos->sum('qtd_voos'), 0, ',', '.') }}</td>
                <td></td>
                <td class="text-end">{{ number_format($voos->sum('qtd_passageiros'), 0, ',', '.') }}</td>
                <td colspan="5"></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema Aeroporto Manager em {{ $dataGeracao }}</p>
        <p>Documento contendo {{ number_format($totalVoos, 0, ',', '.') }} voos registrados para a companhia {{ $companhia->nome }}</p>
    </div>
</body>
</html>