{{-- resources/views/admin/aeroportos/wizard/partials/veiculo-form.blade.php --}}
<div class="veiculo-item">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <strong>Veículo #{{ $index + 1 }}</strong>
        <button type="button" class="btn btn-sm btn-danger remove-veiculo">
            <i class="bi bi-trash"></i> Remover
        </button>
    </div>
    
    <input type="hidden" name="veiculos[{{ $index }}][deposito_id]" value="{{ $depositoId }}">
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Tipo de Veículo *</label>
            <select name="veiculos[{{ $index }}][tipo_veiculo]" class="form-select" required>
                <option value="">Selecione...</option>
                @foreach(\App\Models\Veiculo::TIPOS_VEICULOS as $key => $tipo)
                    <option value="{{ $key }}" {{ isset($veiculo) && $veiculo->tipo_veiculo == $key ? 'selected' : '' }}>
                        {{ $tipo['nome'] }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">Código *</label>
            <input type="text" name="veiculos[{{ $index }}][codigo]" class="form-control" 
                   value="{{ $veiculo->codigo ?? '' }}" required
                   placeholder="Ex: EB-001, CC-023">
            <small class="text-muted">Código único de identificação</small>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" name="veiculos[{{ $index }}][quantidade]" class="form-control" 
                   value="{{ $veiculo->quantidade ?? 1 }}" min="1" step="1">
            <small class="text-muted">Quantidade deste veículo</small>
        </div>
    </div>
</div>