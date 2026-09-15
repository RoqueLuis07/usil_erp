@can('ver_actas_examenes_suficiencia')
    @extends('layouts.master')
    @section('title') Ver Acta de Examen de Suficiencia @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Exámenes de Suficiencia @endslot
            @slot('title') Ver Acta de Examen de Suficiencia @endslot
        @endcomponent

        @include('examenes_suficiencia.actas.modals.show-modals')
        @include('examenes_suficiencia.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar acta de examen de suficiencia</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                <div class="d-flex justify-content-end">
                                    @if ($acta->ubicacion_adjunto)
                                        @can('ver_adjunto_actas_examenes_suficiencia')
                                            <a type="button" class="btn btn-primary me-3" href="{{asset($acta->ubicacion_adjunto)}}" target="_blank">Ver Acta Subido</a>
                                        @endcan
                                        @can('eliminar_adjunto_actas_examenes_suficiencia')
                                            <a type="button" class="btn btn-danger" id="eliminar-adjunto-acta-btn">Eliminar Acta Subido</a>
                                        @endcan
                                    @else
                                        @if ($acta->alumnos->sum('puntos_examen') == 0 && $acta->alumnos->sum('calificacion') == 0)
                                            @can('cargar_puntajes_examenes_suficiencia')
                                                <a type="button" class="btn btn-warning me-2" href="{{route('examenes_suficiencia.create_puntaje', $acta->id)}}">Cargar Puntaje</a>
                                            @endcan
                                        @else
                                            @can('subir_adjunto_actas_examenes_suficiencia')
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
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_acta">N° de Acta</label>
                                    <input type="text" class="form-control text-center" id="numero_acta" value="{{$acta->numero_acta}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="semestre">Período</label>
                                    <input type="text" class="form-control text-center" id="semestre" value="{{$acta->semestre->nombre}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="seccion">Sección</label>
                                    <input type="text" class="form-control text-center" id="seccion" value="{{$acta->seccion}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="turno">Turno</label>
                                    <input type="text" class="form-control text-center" id="turno" @if ($acta->seccion == 'M') value="MAÑANA" @elseif ($acta->seccion == 'T') value="TARDE" @elseif ($acta->seccion == 'N') value="NOCHE" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_evaluacion">Fecha de Evaluación</label>
                                    <input type="text" class="form-control text-center" id="fecha_evaluacion" value="{{\Carbon\Carbon::parse($acta->fecha_evaluacion)->format('d/m/Y')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="evaluacion">Evaluación</label>
                                    <input type="text" class="form-control" id="evaluacion" value="SUFICIENCIA" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$acta->materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$acta->docente->primer_nombre}} {{$acta->docente->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$acta->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$acta->carrera->programa->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap justify-content-between">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Lista de Alumnos</h4>
                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-end">
                                        Cantidad de Alumnos: <b>{{$acta->alumnos->count()}}</b>
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
                                                        <th>Puntaje Obtenido</th>
                                                        <th>Calificación</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($acta->alumnos as $detalle)
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
                                                            <td>{{$detalle->puntos_examen}}</td>
                                                            <td>{{$detalle->calificacion}}</td>
                                                            <td>
                                                                @can('editar_puntajes_examenes_suficiencia')
                                                                    @if ($detalle->puntos_examen && $detalle->calificacion)
                                                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPuntajeModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar Puntaje"><i class="ri-edit-fill"></i></button>
                                                                    @endif
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
                            <a type="button" class="btn btn-danger me-2" href="{{route('examenes_suficiencia.index_actas')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
            @can('eliminar_adjunto_actas_examenes_suficiencia')
                <form action="{{route('examenes_suficiencia.eliminar_acta', $acta->id)}}" method="delete" id="eliminar-acta-form">
                    @csrf
                </form>
            @endcan
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('examenes_suficiencia.actas.scripts.show-scripts')
    @endsection
@endcan
