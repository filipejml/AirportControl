@php
    $isVisible = (bool) $relatorio->visivel_usuario;
    $updatedAt = $relatorio->updated_at;
@endphp

<div class="d-flex flex-wrap gap-2 align-items-center {{ $class ?? '' }}">
    <span class="badge {{ $isVisible ? 'bg-success' : 'bg-secondary' }} js-relatorio-visibilidade-badge">
        <i class="bi {{ $isVisible ? 'bi-eye' : 'bi-eye-slash' }} me-1"></i>
        {{ $isVisible ? 'Visível' : 'Oculto' }}
    </span>

    <span class="badge {{ $isVisible ? 'bg-primary' : 'bg-dark' }} js-relatorio-acesso-badge">
        <i class="bi {{ $isVisible ? 'bi-globe2' : 'bi-shield-lock' }} me-1"></i>
        {{ $isVisible ? 'Público' : 'Admin' }}
    </span>

    <span class="badge bg-light text-dark border">
        <i class="bi bi-clock-history me-1"></i>
        Atualizado {{ $updatedAt ? $updatedAt->format('d/m/Y H:i') : 'sem registro' }}
    </span>
</div>
