@can('crear_actas_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Ver Acta de Evaluación @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Actas de Evaluaciones @endslot
            @slot('title') Ver Acta de Evaluación @endslot
        @endcomponent

        <div class="row">
            @php
                if ($cantidad_actas == 0) {
                    $tipo = 2;
                } else if ($cantidad_actas == 1) {
                    $tipo = 3;
                } else if ($cantidad_actas == 2) {
                    $tipo = 4;
                }
            @endphp
            <form action="{{route('actas_evaluaciones_ubs.generar_actas', ['curso' => $curso->id, 'modulo' => $modulo->id, 'tipo' => $tipo])}}" method="get" target="_blank" id="generate-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar acta de evaluación de {{$curso->nombre_fantasia}} - Módulo {{Str::title($modulo->nombre_fantasia)}}</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                <div class="d-flex justify-content-end">
                                    @can('generar_actas_maestrias_ubs')
                                        @if ($cantidad_actas < 3 && $alumnos->count() > 0)
                                            <button type="button" class="btn btn-success me-2" id="generar-acta-btn">Generar Acta de {{$tipo_examen}}</button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="curso">Curso</label>
                                    <input type="text" class="form-control" id="curso" value="{{$curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="modulo">Modulo</label>
                                    <input type="text" class="form-control" id="modulo" value="{{$modulo->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Docente</label>
                                    <input type="text" class="form-control" id="materia" value="{{$docente->primer_nombre}} {{$docente->primer_apellido}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap justify-content-between">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Alumnos Habilitados</h4>
                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-end">
                                        Cantidad de Alumnos Habilitados: &nbsp;&nbsp; <b>{{$alumnos->count()}}</b>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-6">
                                            <table class="table align-middle table-nowrap text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>N° de Documento</th>
                                                        <th>Alumno</th>
                                                        <th>Puntos Acumulados</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($alumnos as $alumno)
                                                        <tr>
                                                            <td>{{$alumno->numero_documento}}</td>
                                                            <td>
                                                                {{ trim($alumno->primer_apellido . ($alumno->segundo_apellido ? ' ' . $alumno->segundo_apellido : '')) }},
                                                                {{$alumno->primer_nombre}}
                                                                @if ($alumno->segundo_nombre)
                                                                    {{$alumno->segundo_nombre}}
                                                                @endif
                                                                @if ($alumno->tercer_nombre)
                                                                    {{$alumno->tercer_nombre}}
                                                                @endif
                                                            </td>
                                                            <td>{{$alumno->puntos_obtenidos}}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3">No hay alumnos habilitados para generar un acta.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('actas_evaluaciones_ubs.index', $curso->id)}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        @include('ubs.maestrias.actas_evaluaciones.scripts.create-scripts')
    @endsection
@endcan
