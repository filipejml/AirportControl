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
                   value="{{ $veiculo->codigo ?? '' }}" required>
            <small class="text-muted">Código único de identificação</small>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">Fabricante</label>
            <input type="text" name="veiculos[{{ $index }}][fabricante]" class="form-control" 
                   value="{{ $veiculo->fabricante ?? '' }}">
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Modelo</label>
            <input type="text" name="veiculos[{{ $index }}][modelo]" class="form-control" 
                   value="{{ $veiculo->modelo ?? '' }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">Ano de Fabricação</label>
            <input type="number" name="veiculos[{{ $index }}][ano_fabricacao]" class="form-control" 
                   value="{{ $veiculo->ano_fabricacao ?? '' }}" min="1970" max="{{ date('Y') }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label capacidade-label">Capacidade Operacional</label>
            <div class="input-group">
                <input type="number" step="any" name="veiculos[{{ $index }}][capacidade_operacional]" class="form-control" 
                       value="{{ $veiculo->capacidade_operacional ?? '' }}">
                <span class="input-group-text unidade-capacidade">-</span>
            </div>
            <small class="text-muted capacidade-help">Capacidade máxima de operação</small>
        </div>
    </div>
</div>

@if(!isset($veiculo))
<script>
    // Para novos veículos adicionados dinamicamente
    const select = document.querySelector('select[name="veiculos[{{ $index }}][tipo_veiculo]"]');
    if (select) {
        select.addEventListener('change', function() { selectTipo(this); });
    }
    
    const removeBtn = document.querySelector('#veiculos-deposito-{{ $depositoId }} .veiculo-item:last-child .remove-veiculo');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            this.closest('.veiculo-item').remove();
        });
    }
</script>
@endif