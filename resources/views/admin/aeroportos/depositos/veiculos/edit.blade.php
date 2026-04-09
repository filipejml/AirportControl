{{-- resources/views/admin/aeroportos/depositos/veiculos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Veículo - ' . $veiculo->codigo)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">✏️ Editar Veículo</h2>
            <p class="text-muted">
                Depósito: {{ $deposito->nome }} | Aeroporto: {{ $aeroporto->nome_aeroporto }}
                <br>
                <span class="badge bg-{{ $veiculo->tipo_cor }} mt-1">
                    <i class="bi {{ $veiculo->tipo_icone }}"></i> {{ $veiculo->tipo_nome }}
                </span>
            </p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeroportos.depositos.veiculos.update', [$aeroporto, $deposito, $veiculo]) }}" id="veiculoForm">
                @csrf
                @method('PUT')

                {{-- Tipo de Veículo --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Tipo de Veículo *</label>
                    <div class="row g-3" id="tiposContainer">
                        @foreach(\App\Models\Veiculo::TIPOS_VEICULOS as $key => $tipo)
                            <div class="col-md-3 col-sm-6">
                                <div class="tipo-card {{ old('tipo_veiculo', $veiculo->tipo_veiculo) == $key ? 'selected' : '' }}" 
                                     data-tipo="{{ $key }}"
                                     onclick="selectTipo('{{ $key }}')">
                                    <div class="text-center p-3">
                                        <i class="bi {{ $tipo['icone'] }} fs-1"></i>
                                        <h6 class="mt-2 mb-0">{{ $tipo['nome'] }}</h6>
                                        <small class="text-muted">{{ $tipo['descricao'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="tipo_veiculo" id="tipo_veiculo" value="{{ old('tipo_veiculo', $veiculo->tipo_veiculo) }}" required>
                    @error('tipo_veiculo')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Informações Básicas --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="codigo" class="form-label">Código do Veículo *</label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                               id="codigo" name="codigo" value="{{ old('codigo', $veiculo->codigo) }}" required>
                        <small class="text-muted">Ex: EST-001, COMB-002, PUSH-003</small>
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="placa" class="form-label">Placa</label>
                        <input type="text" class="form-control @error('placa') is-invalid @enderror" 
                               id="placa" name="placa" value="{{ old('placa', $veiculo->placa) }}" placeholder="Opcional para equipamentos sem placa">
                        @error('placa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="disponivel" {{ old('status', $veiculo->status) == 'disponivel' ? 'selected' : '' }}>✅ Disponível</option>
                            <option value="em_uso" {{ old('status', $veiculo->status) == 'em_uso' ? 'selected' : '' }}>🔄 Em Uso</option>
                            <option value="manutencao" {{ old('status', $veiculo->status) == 'manutencao' ? 'selected' : '' }}>🔧 Manutenção</option>
                            <option value="inativo" {{ old('status', $veiculo->status) == 'inativo' ? 'selected' : '' }}>⛔ Inativo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" class="form-control" id="fabricante" name="fabricante" 
                               value="{{ old('fabricante', $veiculo->fabricante) }}">
                        <small class="text-muted">Ex: Mercedes-Benz, Scania, TLD, etc.</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" 
                               value="{{ old('modelo', $veiculo->modelo) }}">
                        <small class="text-muted">Ex: Arocs, P360, TX-500</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="ano_fabricacao" class="form-label">Ano de Fabricação</label>
                        <input type="number" class="form-control" id="ano_fabricacao" name="ano_fabricacao" 
                               value="{{ old('ano_fabricacao', $veiculo->ano_fabricacao) }}" 
                               min="1970" max="{{ date('Y') }}">
                    </div>
                </div>

                {{-- Capacidade Operacional --}}
                <div class="row" id="capacidadeRow">
                    <div class="col-md-6 mb-3">
                        <label for="capacidade_operacional" class="form-label" id="capacidadeLabel">
                            @php
                                $capacidadeLabels = [
                                    'esteira_bagagem' => 'Capacidade (kg)',
                                    'caminhao_combustivel' => 'Capacidade (litros)',
                                    'carro_inspecao' => 'Capacidade (N/A)',
                                    'carrinho_bagagem' => 'Capacidade (unidades)',
                                    'caminhao_pushback' => 'Capacidade (toneladas)',
                                    'caminhao_escada' => 'Altura Máxima (metros)',
                                    'caminhao_limpeza' => 'Capacidade (litros)',
                                    'outro' => 'Capacidade'
                                ];
                            @endphp
                            {{ $capacidadeLabels[$veiculo->tipo_veiculo] ?? 'Capacidade Operacional' }}
                        </label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control" id="capacidade_operacional" 
                                   name="capacidade_operacional" 
                                   value="{{ old('capacidade_operacional', $veiculo->capacidade_operacional) }}">
                            <span class="input-group-text" id="unidadeCapacidade">
                                @php
                                    $unidades = [
                                        'esteira_bagagem' => 'kg',
                                        'caminhao_combustivel' => 'litros',
                                        'carro_inspecao' => '-',
                                        'carrinho_bagagem' => 'unidades',
                                        'caminhao_pushback' => 'toneladas',
                                        'caminhao_escada' => 'metros',
                                        'caminhao_limpeza' => 'litros',
                                        'outro' => ''
                                    ];
                                @endphp
                                {{ $unidades[$veiculo->tipo_veiculo] ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Manutenção e Operação --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="horimetro" class="form-label">Horímetro (horas)</label>
                        <input type="number" class="form-control" id="horimetro" name="horimetro" 
                               value="{{ old('horimetro', $veiculo->horimetro) }}" min="0" step="1">
                        <small class="text-muted">Horas totais de operação</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="manutencao_prevista_horas" class="form-label">Manutenção a cada (horas)</label>
                        <input type="number" class="form-control" id="manutencao_prevista_horas" 
                               name="manutencao_prevista_horas" 
                               value="{{ old('manutencao_prevista_horas', $veiculo->manutencao_prevista_horas) }}" min="0">
                        <small class="text-muted">Deixe em branco se não aplicável</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="ultima_manutencao" class="form-label">Última Manutenção</label>
                        <input type="date" class="form-control" id="ultima_manutencao" name="ultima_manutencao" 
                               value="{{ old('ultima_manutencao', $veiculo->ultima_manutencao ? $veiculo->ultima_manutencao->format('Y-m-d') : '') }}">
                        <small class="text-muted">Data da última manutenção realizada</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="proxima_manutencao" class="form-label">Próxima Manutenção (data)</label>
                        <input type="date" class="form-control" id="proxima_manutencao" name="proxima_manutencao" 
                               value="{{ old('proxima_manutencao', $veiculo->proxima_manutencao ? $veiculo->proxima_manutencao->format('Y-m-d') : '') }}">
                        @if($veiculo->proxima_manutencao && $veiculo->proxima_manutencao->isPast())
                            <div class="text-danger mt-1">
                                ⚠️ A manutenção está atrasada desde {{ $veiculo->proxima_manutencao->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ultima_manutencao_horas" class="form-label">Horímetro na Última Manutenção</label>
                        <input type="number" class="form-control" id="ultima_manutencao_horas" name="ultima_manutencao_horas" 
                               value="{{ old('ultima_manutencao_horas', $veiculo->ultima_manutencao_horas) }}" min="0" step="1">
                        <small class="text-muted">Horímetro registrado na última manutenção</small>
                    </div>
                </div>

                {{-- Certificações --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="certificado_operacao" class="form-label">Certificado de Operação</label>
                        <input type="text" class="form-control" id="certificado_operacao" name="certificado_operacao" 
                               value="{{ old('certificado_operacao', $veiculo->certificado_operacao) }}" 
                               placeholder="Número do certificado">
                        <small class="text-muted">Ex: ANAC-2024-001, ISO-9001</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="validade_certificado" class="form-label">Validade do Certificado</label>
                        <input type="date" class="form-control" id="validade_certificado" name="validade_certificado" 
                               value="{{ old('validade_certificado', $veiculo->validade_certificado ? $veiculo->validade_certificado->format('Y-m-d') : '') }}">
                        @if($veiculo->validade_certificado)
                            @php
                                $diasParaVencer = now()->diffInDays($veiculo->validade_certificado, false);
                            @endphp
                            @if($diasParaVencer <= 0)
                                <div class="text-danger mt-1">
                                    ⚠️ Certificado VENCIDO desde {{ $veiculo->validade_certificado->format('d/m/Y') }}
                                </div>
                            @elseif($diasParaVencer <= 30)
                                <div class="text-warning mt-1">
                                    ⚠️ Certificado vence em {{ $diasParaVencer }} dias
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label for="operadores_autorizados" class="form-label">Operadores Autorizados</label>
                    <textarea class="form-control" id="operadores_autorizados" name="operadores_autorizados" 
                              rows="2" placeholder="Nomes ou IDs dos operadores autorizados">{{ old('operadores_autorizados', $veiculo->operadores_autorizados) }}</textarea>
                    <small class="text-muted">Liste os operadores treinados e autorizados para este equipamento</small>
                </div>

                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3" 
                              placeholder="Informações adicionais sobre o veículo...">{{ old('observacoes', $veiculo->observacoes) }}</textarea>
                </div>

                {{-- Histórico de Manutenções (apenas visualização) --}}
                @if($veiculo->historico_manutencoes && count($veiculo->historico_manutencoes) > 0)
                <div class="mb-4">
                    <hr>
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clock-history"></i> Histórico de Manutenções
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Horímetro Antes</th>
                                    <th>Horímetro Depois</th>
                                    <th>Horas Rodadas</th>
                                    <th>Operador</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_reverse($veiculo->historico_manutencoes) as $manutencao)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($manutencao['data'])->format('d/m/Y H:i') }}</td>
                                        <td>{{ $manutencao['descricao'] }}</td>
                                        <td>{{ number_format($manutencao['horimetro_antes'] ?? 0, 0, ',', '.') }} h</td>
                                        <td>{{ number_format($manutencao['horimetro_depois'] ?? 0, 0, ',', '.') }} h</td>
                                        <td>{{ number_format($manutencao['horas_rodadas'] ?? 0, 0, ',', '.') }} h</td>
                                        <td>{{ $manutencao['operador'] ?? 'Sistema' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Atualizar Veículo
                    </button>
                    <a href="{{ route('aeroportos.depositos.veiculos.show', [$aeroporto, $deposito, $veiculo]) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Ver Detalhes
                    </a>
                    <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.tipo-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.tipo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #cbd5e1;
}

.tipo-card.selected {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.tipo-card.selected i {
    color: #3b82f6;
}

.tipo-card.selected h6 {
    color: #1e40af;
}

/* Animações */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>

<script>
function selectTipo(tipo) {
    // Remove selected de todos
    document.querySelectorAll('.tipo-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Adiciona selected ao clicado
    document.querySelector(`.tipo-card[data-tipo="${tipo}"]`).classList.add('selected');
    document.getElementById('tipo_veiculo').value = tipo;
    
    // Atualiza unidade de capacidade baseada no tipo
    const tiposInfo = {
        'esteira_bagagem': { unidade: 'kg', label: 'Capacidade (kg)' },
        'caminhao_combustivel': { unidade: 'litros', label: 'Capacidade (litros)' },
        'carro_inspecao': { unidade: null, label: 'Capacidade (N/A)' },
        'carrinho_bagagem': { unidade: 'unidades', label: 'Capacidade (unidades)' },
        'caminhao_pushback': { unidade: 'toneladas', label: 'Capacidade (toneladas)' },
        'caminhao_escada': { unidade: 'metros', label: 'Altura Máxima (metros)' },
        'caminhao_limpeza': { unidade: 'litros', label: 'Capacidade (litros)' },
        'outro': { unidade: null, label: 'Capacidade' }
    };
    
    const info = tiposInfo[tipo] || tiposInfo['outro'];
    const capacidadeLabel = document.getElementById('capacidadeLabel');
    const unidadeSpan = document.getElementById('unidadeCapacidade');
    
    if (capacidadeLabel) {
        capacidadeLabel.textContent = info.label;
    }
    
    if (unidadeSpan) {
        unidadeSpan.textContent = info.unidade || '-';
    }
}

// Preservar seleção se houver erro de validação
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelecionado = document.getElementById('tipo_veiculo').value;
    if (tipoSelecionado) {
        selectTipo(tipoSelecionado);
    }
    
    // Verificar código duplicado (exceto o próprio)
    const codigoInput = document.getElementById('codigo');
    const codigoOriginal = codigoInput.value;
    
    if (codigoInput) {
        codigoInput.addEventListener('blur', function() {
            const codigo = this.value;
            if (codigo.length > 0 && codigo !== codigoOriginal) {
                fetch('{{ route("aeroportos.depositos.veiculos.check-placa", [$aeroporto, $deposito]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ placa: codigo, id: {{ $veiculo->id }} })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        let feedback = this.parentElement.querySelector('.invalid-feedback');
                        if (!feedback) {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            this.parentElement.appendChild(feedback);
                        }
                        feedback.textContent = 'Este código já está em uso por outro veículo';
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            }
        });
    }
    
    // Validação de datas
    const ultimaManutencao = document.getElementById('ultima_manutencao');
    const proximaManutencao = document.getElementById('proxima_manutencao');
    
    if (ultimaManutencao && proximaManutencao) {
        proximaManutencao.addEventListener('change', function() {
            if (ultimaManutencao.value && this.value && this.value <= ultimaManutencao.value) {
                this.setCustomValidity('A próxima manutenção deve ser após a última manutenção');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Aviso ao mudar status para inativo
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'inativo') {
                if (!confirm('⚠️ Ao marcar como INATIVO, este veículo não poderá ser utilizado até ser reativado. Deseja continuar?')) {
                    this.value = '{{ $veiculo->status }}';
                }
            } else if (this.value === 'manutencao') {
                if (!confirm('🔧 Ao marcar como MANUTENÇÃO, o veículo ficará indisponível. Registre a manutenção no histórico após concluir. Deseja continuar?')) {
                    this.value = '{{ $veiculo->status }}';
                }
            }
        });
    }
});
</script>
@endsection