@extends('layouts.master-academic')
@section('title') Reporte de Docentes @endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Reportes @endslot
        @slot('title') Reporte de Docentes @endslot
    @endcomponent

    <form method="get" action="{{ route('reportes_extension.docentes') }}" class="card">
        <div class="card-header"><h4 class="card-title mb-0">Filtros</h4></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="form-label">Facultad</label>
                    <select name="facultad" class="form-select"><option value="">Todas</option>@foreach ($facultades as $f)<option value="{{ $f->id }}" @selected($filtros['facultad'] == $f->id)>{{ $f->nombre }}</option>@endforeach</select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Carrera</label>
                    <select name="carrera" class="form-select"><option value="">Todas</option>@foreach ($carreras as $c)<option value="{{ $c->id }}" @selected($filtros['carrera'] == $c->id)>{{ $c->nombre_fantasia }}</option>@endforeach</select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Nivel académico</label>
                    <select name="nivel" class="form-select"><option value="">Todos</option>@foreach ($niveles as $n)<option value="{{ $n->id }}" @selected($filtros['nivel'] == $n->id)>{{ $n->nombre }}</option>@endforeach</select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select"><option value="">Todos</option>@foreach ($sexos as $s)<option value="{{ $s->id }}" @selected($filtros['sexo'] == $s->id)>{{ $s->nombre }}</option>@endforeach</select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Año de alta</label>
                    <select name="anio" class="form-select"><option value="">Todos</option>@foreach ($anios as $a)<option value="{{ $a }}" @selected($filtros['anio'] == $a)>{{ $a }}</option>@endforeach</select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Tutor</label>
                    <select name="tutor" class="form-select"><option value="">Todos</option><option value="SI" @selected($filtros['tutor'] === 'SI')>Sí</option><option value="NO" @selected($filtros['tutor'] === 'NO')>No</option></select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Didáctica</label>
                    <select name="didactica" class="form-select"><option value="">Todos</option><option value="SI" @selected($filtros['didactica'] === 'SI')>Sí</option><option value="NO" @selected($filtros['didactica'] === 'NO')>No</option></select>
                </div>
                <div class="col-lg-8 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success">Aplicar</button>
                    <a href="{{ route('reportes_extension.docentes') }}" class="btn btn-warning">Limpiar</a>
                    <a href="{{ route('reportes_extension.docentes', array_merge(array_filter($filtros, fn ($v) => $v !== null && $v !== ''), ['exportar' => 'csv'])) }}" class="btn btn-warning ms-auto"><i class="ri-download-line align-bottom me-1"></i>Exportar CSV</a>
                </div>
            </div>
        </div>
    </form>

    <div class="row text-center mb-3 g-3">
        @php
            $tiles = [['Docentes registrados', $resumen['docentes']], ['Tutores', $resumen['tutores']], ['Con capacitación didáctica', $resumen['didactica']], ['Extensiones realizadas', $resumen['extensiones']], ['Horas de extensión', $resumen['horas']]];
        @endphp
        @foreach ($tiles as [$etiqueta, $valor])
            <div class="col"><div class="card mb-0"><div class="card-body py-3"><div class="fs-3 fw-bold">{{ $valor }}</div><div class="text-muted" style="font-size: 12px">{{ $etiqueta }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Docentes por facultad', 'datos' => $porFacultad])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Docentes por carrera', 'datos' => $porCarrera])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Nivel académico', 'datos' => $porNivel])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Sexo', 'datos' => $porSexo, 'destacar' => false])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Rango de edad', 'datos' => $porEdad, 'destacar' => false])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Año de alta', 'datos' => $porAnio, 'destacar' => false])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Nacionalidad', 'datos' => $porNacionalidad])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Departamento', 'datos' => $porDepartamento])</div>
        <div class="col-lg-4">@include('reportes_extension.partials.ranking', ['titulo' => 'Ciudad', 'datos' => $porCiudad])</div>
        <div class="col-lg-6">@include('reportes_extension.partials.ranking', ['titulo' => 'Extensión realizada por carrera (estudiantes participantes)', 'datos' => $extPorCarrera, 'unidad' => ' ext.'])</div>
        <div class="col-lg-6">@include('reportes_extension.partials.ranking', ['titulo' => 'Extensión realizada por semestre de los estudiantes', 'datos' => $extPorSemestre, 'unidad' => ' est.'])</div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Detalle de docentes ({{ $filas->count() }})</h4></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-nowrap mb-0">
                    <thead>
                        <tr><th>Docente</th><th>Nivel</th><th>Sexo</th><th>Carreras</th><th>Facultades</th><th class="text-center">Tutor</th><th class="text-center">Didáctica</th><th class="text-center">Extensiones</th><th class="text-center">Estud.</th><th class="text-center">Horas</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($filas as $fila)
                            <tr>
                                <td><a href="{{ route('docentes.show', $fila->id) }}">{{ $fila->nombre }}</a></td>
                                <td>{{ $fila->nivel }}</td>
                                <td>{{ $fila->sexo }}</td>
                                <td>{{ $fila->carreras->implode(', ') ?: '-' }}</td>
                                <td>{{ $fila->facultades->implode(', ') ?: '-' }}</td>
                                <td class="text-center">{{ $fila->tutor ? 'Sí' : 'No' }}</td>
                                <td class="text-center">{{ $fila->didactica ? 'Sí' : 'No' }}</td>
                                <td class="text-center">{{ $fila->extensiones }}</td>
                                <td class="text-center">{{ $fila->ext_estudiantes }}</td>
                                <td class="text-center">{{ number_format($fila->ext_horas, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No hay docentes para los filtros elegidos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
