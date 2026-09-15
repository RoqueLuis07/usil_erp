@can('cargar_puntajes_examenes_suficiencia')
    @extends('layouts.master')
    @section('title') Cargar Puntajes de Examen de Suficiencia @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Acta de Examen de Suficiencia @endslot
            @slot('title') Cargar Puntajes Examen de Suficiencia @endslot
        @endcomponent

        @include('examenes_suficiencia.actas.modals.show-modals')
        @include('examenes_suficiencia.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('examenes_suficiencia.store_puntaje', $acta->id)}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Cargar puntajes de acta de examen de suficiencia</h4>
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
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$acta->carrera->nombre_fantasia}}" readonly>
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
                                    @foreach ($acta->alumnos as $key => $detalle)
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-3 mb-3 text-center">
                                                <label class="form-label" for="alumno-{{$key}}">Alumno</label>
                                                <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$detalle->alumno_id}}">
                                                <input type="text" class="form-control text-center" id="alumno-{{$key}}" value="{{$detalle->alumno->primer_apellido}} {{$detalle->alumno->primer_nombre}}" readonly>
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                <label class="form-label" for="alumno_documento-{{$key}}">N° Documento</label>
                                                <input type="text" class="form-control text-center" id="alumno-documento-{{$key}}" value="{{$detalle->alumno->numero_documento}}" readonly>
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                <label class="form-label" for="puntos_obtenidos-{{$key}}">Puntaje <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center puntos_obtenidos puntos_obtenidos-{{$key}} @error('detalles.' . $key . '.puntos_obtenidos') is-invalid @enderror" id="puntos_obtenidos-{{$key}}" name="detalles[{{$key}}][puntos_obtenidos]" data-id="{{$key}}">
                                                @error('detalles.' . $key . '.puntos_obtenidos')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                <label class="form-label" for="calificacion-{{$key}}">Calificación</label>
                                                <input type="text" class="form-control text-center calificacion calificacion-{{$key}}" id="calificacion-{{$key}}" name="detalles[{{$key}}][calificacion]" readonly>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-id="{{$acta->id}}">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('examenes_suficiencia.puntajes.scripts.create-scripts')
    @endsection
@endcan
