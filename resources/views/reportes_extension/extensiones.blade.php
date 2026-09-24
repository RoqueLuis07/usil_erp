@extends('layouts.master-academic')
@section('title') Reporte de Extensión @endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Reportes @endslot
        @slot('title') Reporte de Extensión @endslot
    @endcomponent

    <form method="get" action="{{ route('reportes_extension.extensiones') }}" class="card">
        <div class="card-header"><h4 class="card-title mb-0">Filtros</h4></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-1 col-6">
                    <label class="form-label">Año</label>
                    <select name="anio" class="form-select"><option value="">Todos</option>@foreach ($anios as $anio)<option value="{{ $anio }}" @selected($filtros['anio'] == $anio)>{{ $anio }}</option>@endforeach</select>
                </div>
                <div class="col-lg-2 col-6">
                    <label class="form-label">Mes</label>
                    <select name="mes" class="form-select"><option value="">Todos</option>@foreach ($meses as $n => $mes)<option value="{{ $n }}" @selected($filtros['mes'] == $n)>{{ $mes }}</option>@endforeach</select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Facultad</label>
                    <select name="facultad" class="form-select"><option value="">Todas</option>@foreach ($facultades as $f)<option value="{{ $f->id }}" @selected($filtros['facultad'] == $f->id)>{{ $f->nombre }}</option>@endforeach</select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Carrera</label>
                    <select name="carrera" class="form-select"><option value="">Todas</option>@foreach ($carreras as $c)<option value="{{ $c->id }}" @selected($filtros['carrera'] == $c->id)>{{ $c->nombre_fantasia }}</option>@endforeach</select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Docente responsable</label>
                    <select name="docente" class="form-select"><option value="">Todos</option>@foreach ($docentes as $d)<option value="{{ $d->id }}" @selected($filtros['docente'] == $d->id)>{{ $d->primer_nombre }} {{ $d->primer_apellido }}</option>@endforeach</select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Tipo de actividad</label>
                    <select name="tipo" class="form-select"><option value="">Todos</option>@foreach ($tipos as $t)<option value="{{ $t->id }}" @selected($filtros['tipo'] == $t->id)>{{ $t->nombre }}</option>@endforeach</select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select"><option value="">Todos (menos rechazados)</option>@foreach ($estados as $k => $e)<option value="{{ $k }}" @selected($filtros['estado'] == $k)>{{ $e }}</option>@endforeach</select>
                </div>
                <div class="col-lg-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success">Aplicar</button>
                    <a href="{{ route('reportes_extension.extensiones') }}" class="btn btn-warning">Limpiar</a>
                    <a href="{{ route('reportes_extension.extensiones', array_merge(array_filter($filtros, fn ($v) => $v !== null && $v !== ''), ['exportar' => 'csv'])) }}" class="btn btn-warning ms-auto"><i class="ri-download-line align-bottom me-1"></i>Exportar CSV</a>
                </div>
            </div>
        </div>
    </form>

    <div class="row text-center mb-3 g-3">
        @php
            $tiles = [['Extensiones', $resumen['extensiones']], ['Estudiantes participantes', $resumen['estudiantes']], ['Horas asignadas', number_format($resumen['horas'], 0, ',', '.')], ['Con certificado', $resumen['con_certificado']], ['Certificados cargados', $resumen['certificados_cargados']]];
        @endphp
        @foreach ($tiles as [$etiqueta, $valor])
            <div class="col"><div class="card mb-0"><div class="card-body py-3"><div class="fs-3 fw-bold">{{ $valor }}</div><div class="text-muted" style="font-size: 12px">{{ $etiqueta }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Tipo de actividad más realizado', 'datos' => $porTipo, 'unidad' => ' ext.'])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Carreras que más participan (extensiones)', 'datos' => $porCarrera, 'unidad' => ' ext.'])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Facultades (extensiones)', 'datos' => $porFacultad, 'unidad' => ' ext.'])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Estudiantes por carrera', 'datos' => $estudiantesPorCarrera, 'unidad' => ' est.'])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Semestre de los estudiantes que participan', 'datos' => $porSemestre, 'unidad' => ' est.'])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Docentes que más extensiones hacen', 'datos' => $porDocente, 'unidad' => ' ext.'])</div>
        <div class="col-lg-6">@include('reportes_extension.partials.ranking', ['titulo' => 'Extensiones por mes', 'datos' => $porMes, 'unidad' => ' ext.', 'destacar' => false, 'limite' => 24])</div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Detalle de extensiones ({{ $filas->count() }})</h4></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-nowrap mb-0">
                    <thead>
                        <tr><th>Proyecto</th><th>Tipo</th><th>Inicio</th><th>Docente</th><th class="text-center">Horas</th><th class="text-center">Estud.</th><th>Carreras</th><th>Facultades</th><th>Certificado</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($filas as $fila)
                            <tr>
                                <td><a href="{{ route('extensiones_universitarias.show', $fila->id) }}">{{ $fila->nombre }}</a></td>
                                <td>{{ $fila->tipo }}</td>
                                <td>{{ $fila->fecha_inicio }}</td>
                                <td>{{ $fila->docente }}</td>
                                <td class="text-center">{{ number_format($fila->horas, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $fila->estudiantes }}</td>
                                <td>{{ $fila->carreras->implode(', ') ?: '-' }}</td>
                                <td>{{ $fila->facultades->implode(', ') ?: '-' }}</td>
                                <td>@if ($fila->tiene_certificado)<span class="badge bg-success-subtle text-success">SÍ · {{ $fila->certificados_cargados }}/{{ $fila->estudiantes }}</span>@else<span class="badge bg-secondary-subtle text-secondary">NO</span>@endif</td>
                                <td>{{ $fila->estado_texto }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No hay extensiones para los filtros elegidos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Extensiones con certificado ({{ $conCertificado->count() }})</h4></div>
        <div class="card-body">
            @forelse ($conCertificado as $fila)
                <div class="mb-1" style="font-size: 12.5px"><b>{{ $fila->nombre }}</b> — {{ $fila->tipo }}, {{ $fila->fecha_inicio }} · certificados cargados {{ $fila->certificados_cargados }} de {{ $fila->estudiantes }} estudiantes</div>
            @empty
                <span class="text-muted">Ninguna de las extensiones filtradas emite certificado.</span>
            @endforelse
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
