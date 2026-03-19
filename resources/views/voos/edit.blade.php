{{-- resources/views/voos/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Editar Voo</h1>

    <form action="{{ route('voos.update', $voo) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ID do Voo --}}
            <div>
                <label for="id_voo" class="block text-sm font-medium text-gray-700 mb-2">ID do Voo (LL-NNNN)*</label>
                <input type="text" name="id_voo" id="id_voo" value="{{ old('id_voo', $voo->id_voo) }}" required
                    pattern="[A-Za-z]{2}-\d{4}"
                    placeholder="ex: AA-1234"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('id_voo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Aeroporto --}}
            <div>
                <label for="aeroporto_id" class="block text-sm font-medium text-gray-700 mb-2">Aeroporto*</label>
                <select name="aeroporto_id" id="aeroporto_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione um aeroporto</option>
                    @foreach($aeroportos as $aeroporto)
                        <option value="{{ $aeroporto->id }}" {{ old('aeroporto_id', $voo->aeroporto_id) == $aeroporto->id ? 'selected' : '' }}>
                            {{ $aeroporto->nome_aeroporto }}
                        </option>
                    @endforeach
                </select>
                @error('aeroporto_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Companhia Aérea --}}
            <div>
                <label for="companhia_aerea_id" class="block text-sm font-medium text-gray-700 mb-2">Companhia Aérea*</label>
                <select name="companhia_aerea_id" id="companhia_aerea_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione uma companhia</option>
                    @foreach($companhias as $companhia)
                        <option value="{{ $companhia->id }}" {{ old('companhia_aerea_id', $voo->companhia_aerea_id) == $companhia->id ? 'selected' : '' }}>
                            {{ $companhia->nome }}
                        </option>
                    @endforeach
                </select>
                @error('companhia_aerea_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Modelo da Aeronave --}}
            <div>
                <label for="aeronave_id" class="block text-sm font-medium text-gray-700 mb-2">Modelo da Aeronave*</label>
                <select name="aeronave_id" id="aeronave_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione uma aeronave</option>
                    @foreach($aeronaves as $aeronave)
                        <option value="{{ $aeronave->id }}" 
                                data-companhia="{{ $aeronave->companhias->pluck('id')->implode(',') }}"
                                data-porte="{{ $aeronave->porte }}"
                                data-capacidade="{{ $aeronave->capacidade }}"
                                {{ old('aeronave_id', $voo->aeronave_id) == $aeronave->id ? 'selected' : '' }}>
                            {{ $aeronave->modelo }} ({{ $aeronave->fabricante->nome ?? 'Sem fabricante' }}) - Cap: {{ $aeronave->capacidade }} - Porte: {{ $aeronave->porte }}
                        </option>
                    @endforeach
                </select>
                @error('aeronave_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo de Voo --}}
            <div>
                <label for="tipo_voo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Voo*</label>
                <select name="tipo_voo" id="tipo_voo" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="Regular" {{ old('tipo_voo', $voo->tipo_voo) == 'Regular' ? 'selected' : '' }}>Regular</option>
                    <option value="Charter" {{ old('tipo_voo', $voo->tipo_voo) == 'Charter' ? 'selected' : '' }}>Charter</option>
                </select>
                @error('tipo_voo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quantidade de Voos --}}
            <div>
                <label for="qtd_voos" class="block text-sm font-medium text-gray-700 mb-2">Quantidade de Voos*</label>
                <input type="number" name="qtd_voos" id="qtd_voos" value="{{ old('qtd_voos', $voo->qtd_voos) }}" required min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('qtd_voos')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Horário do Voo --}}
            <div>
                <label for="horario_voo" class="block text-sm font-medium text-gray-700 mb-2">Horário do Voo*</label>
                <select name="horario_voo" id="horario_voo" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="EAM" {{ old('horario_voo', $voo->horario_voo) == 'EAM' ? 'selected' : '' }}>EAM (Early Morning)</option>
                    <option value="AM" {{ old('horario_voo', $voo->horario_voo) == 'AM' ? 'selected' : '' }}>AM (Morning)</option>
                    <option value="AN" {{ old('horario_voo', $voo->horario_voo) == 'AN' ? 'selected' : '' }}>AN (Afternoon)</option>
                    <option value="PM" {{ old('horario_voo', $voo->horario_voo) == 'PM' ? 'selected' : '' }}>PM (Evening)</option>
                    <option value="ALL" {{ old('horario_voo', $voo->horario_voo) == 'ALL' ? 'selected' : '' }}>ALL (All Day)</option>
                </select>
                @error('horario_voo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notas (opcionais) --}}
            <div class="col-span-2">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Notas (opcionais)</h3>
            </div>

            {{-- Nota Objetivo --}}
            <div>
                <label for="nota_obj" class="block text-sm font-medium text-gray-700 mb-2">Nota Objetivo</label>
                <select name="nota_obj" id="nota_obj"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="A" {{ old('nota_obj', $voo->nota_obj_letra) == 'A' ? 'selected' : '' }}>A (10)</option>
                    <option value="B" {{ old('nota_obj', $voo->nota_obj_letra) == 'B' ? 'selected' : '' }}>B (9)</option>
                    <option value="C" {{ old('nota_obj', $voo->nota_obj_letra) == 'C' ? 'selected' : '' }}>C (8)</option>
                    <option value="D" {{ old('nota_obj', $voo->nota_obj_letra) == 'D' ? 'selected' : '' }}>D (6)</option>
                    <option value="E" {{ old('nota_obj', $voo->nota_obj_letra) == 'E' ? 'selected' : '' }}>E (4)</option>
                    <option value="F" {{ old('nota_obj', $voo->nota_obj_letra) == 'F' ? 'selected' : '' }}>F (2)</option>
                </select>
            </div>

            {{-- Nota Pontualidade --}}
            <div>
                <label for="nota_pontualidade" class="block text-sm font-medium text-gray-700 mb-2">Nota Pontualidade</label>
                <select name="nota_pontualidade" id="nota_pontualidade"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="A" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'A' ? 'selected' : '' }}>A (10)</option>
                    <option value="B" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'B' ? 'selected' : '' }}>B (9)</option>
                    <option value="C" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'C' ? 'selected' : '' }}>C (8)</option>
                    <option value="D" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'D' ? 'selected' : '' }}>D (6)</option>
                    <option value="E" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'E' ? 'selected' : '' }}>E (4)</option>
                    <option value="F" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'F' ? 'selected' : '' }}>F (2)</option>
                </select>
            </div>

            {{-- Nota Serviços --}}
            <div>
                <label for="nota_servicos" class="block text-sm font-medium text-gray-700 mb-2">Nota Serviços</label>
                <select name="nota_servicos" id="nota_servicos"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="A" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'A' ? 'selected' : '' }}>A (10)</option>
                    <option value="B" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'B' ? 'selected' : '' }}>B (9)</option>
                    <option value="C" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'C' ? 'selected' : '' }}>C (8)</option>
                    <option value="D" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'D' ? 'selected' : '' }}>D (6)</option>
                    <option value="E" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'E' ? 'selected' : '' }}>E (4)</option>
                    <option value="F" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'F' ? 'selected' : '' }}>F (2)</option>
                </select>
            </div>

            {{-- Nota Pátio --}}
            <div>
                <label for="nota_patio" class="block text-sm font-medium text-gray-700 mb-2">Nota Pátio</label>
                <select name="nota_patio" id="nota_patio"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="A" {{ old('nota_patio', $voo->nota_patio_letra) == 'A' ? 'selected' : '' }}>A (10)</option>
                    <option value="B" {{ old('nota_patio', $voo->nota_patio_letra) == 'B' ? 'selected' : '' }}>B (9)</option>
                    <option value="C" {{ old('nota_patio', $voo->nota_patio_letra) == 'C' ? 'selected' : '' }}>C (8)</option>
                    <option value="D" {{ old('nota_patio', $voo->nota_patio_letra) == 'D' ? 'selected' : '' }}>D (6)</option>
                    <option value="E" {{ old('nota_patio', $voo->nota_patio_letra) == 'E' ? 'selected' : '' }}>E (4)</option>
                    <option value="F" {{ old('nota_patio', $voo->nota_patio_letra) == 'F' ? 'selected' : '' }}>F (2)</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('voos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Cancelar
            </a>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Atualizar Voo
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Filtrar aeronaves por companhia selecionada
    $('#companhia_aerea_id').change(function() {
        var companhiaId = $(this).val();
        
        $('#aeronave_id option').each(function() {
            var $option = $(this);
            if ($option.val() === '') return;
            
            var companhias = $option.data('companhia') ? $option.data('companhia').split(',') : [];
            
            if (companhiaId && companhias.indexOf(companhiaId.toString()) === -1) {
                $option.hide();
            } else {
                $option.show();
            }
        });
    });

    // Trigger change on page load
    if ($('#companhia_aerea_id').val()) {
        $('#companhia_aerea_id').trigger('change');
    }
});
</script>
@endsection