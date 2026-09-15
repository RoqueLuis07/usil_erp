@can('ver_actas_tutorias')
    @extends('layouts.master')
    @section('title') Ver Acta de Examen de Tutoría @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Tutorías @endslot
            @slot('title') Ver Acta de Examen Tutoría @endslot
        @endcomponent

        @include('tutorias.evaluaciones.actas.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar acta de examen de tutoría</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                <div class="d-flex justify-content-end">
                                    @can('generar_actas_tutorias')
                                        @if (!$tutoria->acta && $alumnos->count() > 0)
                                            <button type="button" class="btn btn-success me-2" id="generar-acta-btn" data-url="{{route('tutorias_evaluaciones.generate_acta', $tutoria->id)}}">Generar Acta de Examen</button>
                                    @endcan
                                        @else
                                            @can('reimprimir_actas_tutorias')
                                                <a type="button" class="btn btn-primary me-2" href="{{asset($tutoria->acta->ubicacion_acta)}}" target="_blank">Reimprimir</a>
                                            @endcan
                                            @if ($tutoria->acta->ubicacion_adjunto)
                                                @can('eliminar_adjunto_actas_tutorias')
                                                    <a type="button" class="btn btn-danger" id="eliminar-adjunto-acta-btn">Eliminar Acta Subido</a>
                                                @endcan
                                            @else
                                                @can('subir_adjunto_actas_tutorias')
                                                    <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#subirActaModal" id="subir-acta-btn">Subir Acta</button>
                                                @endcan
                                            @endif
                                        @endif
                                </div>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$tutoria->materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Docente</label>
                                    <input type="text" class="form-control" id="materia" value="{{$tutoria->docente->primer_nombre}} {{$tutoria->docente->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="numero_acta">N° de Acta</label>
                                    <input type="text" class="form-control" id="numero_acta" @if ($tutoria->acta) value="{{$tutoria->acta->numero_acta}}" @endif readonly>
                                </div>
                                <div class="col-lg-5 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$tutoria->semestre->nombre}}" readonly>
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
                                        <h4 class="card-title mb-0">@if ($tutoria->estado == 'FI') Lista de Alumnos @else Alumnos Habilitados @endif</h4>
                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-end">
                                        Cantidad de Alumnos @if ($tutoria->estado != 'FI') Habilitados @endif: &nbsp;&nbsp; <b>{{$tutoria->alumnos->count()}}</b>
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
                                                        <th>Carrera</th>
                                                        <th>Asistencia</th>
                                                        @if ($tutoria->estado == 'FI')
                                                            <th>Puntaje Obtenido</th>
                                                            <th>Calificación</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($alumnos as $detalle)
                                                        <tr>
                                                            <td>{{number_format($detalle->alumno->numero_documento, 0, ',', '.')}}</td>
                                                            <td>
                                                                {{ trim($detalle->alumno->primer_apellido . ($detalle->alumno->segundo_apellido ? ' ' . $detalle->alumno->segundo_apellido : '')) }},
                                                                {{$detalle->alumno->primer_nombre}}
                                                                @if ($detalle->alumno->segundo_nombre)
                                                                    {{$detalle->alumno->segundo_nombre}}
                                                                @endif
                                                                @if ($detalle->alumno->tercer_nombre)
                                                                    {{$detalle->alumno->tercer_nombre}}
                                                                @endif
                                                            </td>
                                                            <td>{{$detalle->carrera->nombre_fantasia}}</td>
                                                            <td>{{($detalle->cantidad_asistencias / $tutoria->cantidad_clases) * 100}} %</td>
                                                            @if ($tutoria->estado == 'FI')
                                                                <td>{{$detalle->puntos_obtenidos}}</td>
                                                                <td>{{$detalle->calificacion}}</td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="@if ($tutoria->estado == 'FI') 5 @else 3 @endif">No hay alumnos habilitados para generar un acta.</td>
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
                            <a type="button" class="btn btn-danger me-2" href="{{route('tutorias.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
            @can('eliminar_adjunto_actas_tutorias')
                <form action="{{route('tutorias_evaluaciones.eliminar_acta', $tutoria->id)}}" method="delete" id="eliminar-acta-form">
                    @csrf
                </form>
            @endcan
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('tutorias.evaluaciones.actas.scripts.show-scripts')
    @endsection
@endcan
