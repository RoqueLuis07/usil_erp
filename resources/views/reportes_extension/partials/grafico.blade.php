@php
    // Widget de gráfico real (ApexCharts) para reportes de gestión.
    // $datos: array ['etiqueta' => cantidad, ...]; $tipo: bar | donut | line.
    $tipo = $tipo ?? 'bar';
    $unidad = $unidad ?? 'Cantidad';
    $limite = $limite ?? 12;
    $datosLimitados = array_slice($datos, 0, $limite, true);
    $categorias = array_keys($datosLimitados);
    $valores = array_values($datosLimitados);
    $chartId = 'chart_' . uniqid();
    $alto = $tipo === 'bar' ? max(220, count($categorias) * 34) : 260;
@endphp
<div class="card h-100">
    <div class="card-header"><h4 class="card-title mb-0" style="font-size: 13px">{{ $titulo }}</h4></div>
    <div class="card-body">
        @if (count($datosLimitados))
            <div id="{{ $chartId }}"></div>
            @if (count($datos) > $limite)
                <div class="text-muted mt-1" style="font-size: 11px">y {{ count($datos) - $limite }} más (ver el CSV completo)</div>
            @endif
        @else
            <span class="text-muted">Sin datos para los filtros elegidos.</span>
        @endif
    </div>
</div>
@if (count($datosLimitados))
    <script>
        (function () {
            var categorias = @json($categorias);
            var valores = @json($valores);
            var paleta = ['#0f4c5c', '#1f6b4f', '#8a4d13', '#8a2b2b', '#5c6a6e', '#0c3b47', '#3a7d8f', '#b8860b'];
            var opciones;
            if ('{{ $tipo }}' === 'donut') {
                opciones = {
                    chart: { type: 'donut', height: {{ $alto }}, fontFamily: "'Open Sans', sans-serif" },
                    labels: categorias, series: valores, colors: paleta,
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { style: { fontSize: '10px' } },
                };
            } else if ('{{ $tipo }}' === 'line') {
                opciones = {
                    chart: { type: 'area', height: {{ $alto }}, toolbar: { show: false }, fontFamily: "'Open Sans', sans-serif" },
                    series: [{ name: '{{ $unidad }}', data: valores }],
                    xaxis: { categories: categorias, labels: { style: { fontSize: '10px' } } },
                    yaxis: { labels: { style: { fontSize: '10px' } } },
                    colors: ['#0f4c5c'],
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
                    dataLabels: { enabled: false },
                    grid: { borderColor: '#e4eaec' },
                };
            } else {
                opciones = {
                    chart: { type: 'bar', height: {{ $alto }}, toolbar: { show: false }, fontFamily: "'Open Sans', sans-serif" },
                    series: [{ name: '{{ $unidad }}', data: valores }],
                    plotOptions: { bar: { horizontal: true, borderRadius: 2, barHeight: '55%' } },
                    xaxis: { categories: categorias, labels: { style: { fontSize: '10px' } } },
                    colors: ['#0f4c5c'],
                    dataLabels: { enabled: true, style: { fontSize: '10px', colors: ['#1b2427'] }, offsetX: 12 },
                    grid: { borderColor: '#e4eaec' },
                };
            }
            new ApexCharts(document.querySelector('#{{ $chartId }}'), opciones).render();
        })();
    </script>
@endif
