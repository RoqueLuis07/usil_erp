@php
    $datos = $datos ?? [];
    $maximo = count($datos) ? max($datos) : 0;
    $unidad = $unidad ?? '';
    $limite = $limite ?? 10;
@endphp
<div class="card h-100">
    <div class="card-header"><h4 class="card-title mb-0" style="font-size: 13px">{{ $titulo }}</h4></div>
    <div class="card-body" style="font-size: 12.5px">
        @forelse (array_slice($datos, 0, $limite, true) as $etiqueta => $cantidad)
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span>{{ $etiqueta }}@if ($loop->first && $maximo > 0 && ($destacar ?? true)) <span class="badge bg-success-subtle text-success ms-1">MÁS</span>@endif</span>
                    <span class="ac-mono">{{ $cantidad }}{{ $unidad }}</span>
                </div>
                <div style="height: 6px; background: #eef2f3;"><div style="height: 6px; width: {{ $maximo ? round($cantidad / $maximo * 100) : 0 }}%; background: var(--ac-accent);"></div></div>
            </div>
        @empty
            <span class="text-muted">Sin datos para los filtros elegidos.</span>
        @endforelse
        @if (count($datos) > $limite)
            <div class="text-muted mt-1" style="font-size: 11px">y {{ count($datos) - $limite }} más (ver el CSV completo)</div>
        @endif
    </div>
</div>
