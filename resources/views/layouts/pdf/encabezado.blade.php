{{-- Encabezado común de los informes PDF.
     Variables opcionales: $subtitulo (texto bajo el nombre), $mostrarRuc (imprime el RUC si la empresa lo tiene). --}}
@php
    $logo_ruta = public_path(config('institucion.logo'));
    $emp = $empresa ?? null;
@endphp
<div id="encabezado">
    <div id="logo">
        @if (is_file($logo_ruta))
            <img src="{{ $logo_ruta }}" alt="{{ config('institucion.nombre') }}" style="width: 80px; height: auto">
        @endif
    </div>
    <div id="datos">
        <div class="mb-1" style="font-size: 14px"><b>{{ config('institucion.nombre') }}</b></div>
        @if (!empty($subtitulo))
            <div class="mb-1" style="font-size: 12px"><b>{{ $subtitulo }}</b></div>
        @elseif (config('institucion.lema'))
            <div class="mb-1" style="font-size: 10px"><i>{{ config('institucion.lema') }}</i></div>
        @endif
        @if (!empty($mostrarRuc) && $emp && $emp->ruc)
            <div class="mb-1" style="font-size: 12px"><b>RUC: {{ $emp->ruc }}</b></div>
        @endif
        @if ($emp && $emp->direccion)
            <div style="font-size: 10px">{{ $emp->direccion }}</div>
        @endif
        @if ($emp && (optional($emp->ciudad)->nombre || optional($emp->pais)->nombre))
            <div style="font-size: 10px">{{ collect([optional($emp->ciudad)->nombre, optional($emp->pais)->nombre])->filter()->implode(', ') }}</div>
        @endif
        @if ($emp && $emp->telefono)
            <div style="font-size: 10px">Teléfono: {{ $emp->telefono }}</div>
        @endif
    </div>
</div>
