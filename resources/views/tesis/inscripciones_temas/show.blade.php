@can('ver_inscripciones_tesis')
    @extends('layouts.master')
    @section('title') Ver Tema de Trabajo Final de Grado @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            @media screen and (max-width: 600px) {
                #div-subir {
                    margin-left: 2.6em;
                    margin-top: -0.5em;
                }
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Temas de Trabajo Final de Grado @endslot
            @slot('title') Ver Tema de Trabajo Final de Grado @endslot
        @endcomponent

        @include('tesis.inscripciones_temas.scripts.messages-scripts')
        @include('tesis.inscripciones_temas.modals.show-modals')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar tema de trabajo final de grado</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @if ($inscripcion->estado == 'PE')
                                    @can('aprobar_tutor_inscripciones_tesis')
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#approveTutorModal">Aprobar (Tutor)</button>
                                    @endcan
                                    @can('rechazar_inscripciones_tesis')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Rechazar</button>
                                    @endcan
                                @elseif ($inscripcion->estado == 'AT')
                                    @can('aprobar_coordinacion_inscripciones_tesis')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveCoordinacionModal">Aprobar (Coord.)</button>
                                    @endcan
                                    @can('anular_tutor_inscripciones_tesis')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveTutorModal">Anular Aprobación (Tutor)</button>
                                    @endcan
                                @elseif ($inscripcion->estado == 'AC')
                                    @can('anular_coordinacion_inscripciones_tesis')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveCoordinacionModal">Anular Aprobación (Coord.)</button>
                                    @endcan
                                @elseif ($inscripcion->estado == 'RE')
                                    @can('anular_rechazo_inscripciones_tesis')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#unrejectModal">Anular Rechazo</button>
                                    @endcan
                                @elseif ($inscripcion->estado == 'AB' || $inscripcion->estado == 'FE' || $inscripcion->estado == 'EN' || $inscripcion->estado == 'RR')
                                    @can('generar_actas_tesis')
                                        @if ($inscripcion->rubrica && ($inscripcion->estado == 'AB' || $inscripcion->estado == 'FE'))
                                            <a type="button" class="btn btn-success me-2" href="{{route('inscripciones_temas_tesis.generate_acta', $inscripcion->id)}}" target="_blank" id="generar-btn">@if (!$inscripcion->acta_generado) Generar Acta @else Regenerar Acta @endif</a>
                                        @endif
                                    @endcan
                                    @can('cargar_rubricas_alumnos_tesis')
                                        @if ($inscripcion->rubrica)
                                            @if ($inscripcion->acta_generado)
                                                @if (!$inscripcion->calificacion)
                                                    <a type="button" class="btn btn-warning me-2" href="{{route('cargar_rubricas_tesis.create_defensa', $inscripcion->id)}}">Cargar Puntaje Defensa</a>
                                                @else
                                                    <a type="button" class="btn btn-info" href="{{route('cargar_rubricas_tesis.show_defensa', $inscripcion->id)}}">Ver Puntaje Defensa</a>
                                                @endif
                                            @endif
                                            <a type="button" class="btn btn-primary" href="{{route('cargar_rubricas_tesis.show_proceso', $inscripcion->id)}}">Ver Rúbrica Proceso</a>
                                        @else
                                            <a type="button" class="btn btn-warning" href="{{route('cargar_rubricas_tesis.create_proceso', $inscripcion->id)}}">Cargar Rúbrica Proceso</a>
                                        @endif
                                    @endcan
                                @endif
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{\Carbon\Carbon::parse($inscripcion->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}} " readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="alumno_documento">N° Documento</label>
                                    <input type="text" class="form-control" id="alumno_documento" value="{{$inscripcion->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$inscripcion->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$inscripcion->programa->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold
                                        @if ($inscripcion->estado == 'PE' || $inscripcion->estado == 'RE' || $inscripcion->estado == 'RR')
                                            text-danger
                                        @elseif ($inscripcion->estado == 'AT' || $inscripcion->estado == 'EC' || $inscripcion->estado == 'PC' || $inscripcion->estado == 'BC' || $inscripcion->estado == 'FE')
                                            text-warning
                                        @elseif ($inscripcion->estado == 'AC' || $inscripcion->estado == 'AA' || $inscripcion->estado == 'AP' || $inscripcion->estado == 'AB' || $inscripcion->estado == 'PA' || $inscripcion->estado == 'EN')
                                            text-success
                                        @endif"
                                        id="estado"
                                        @if ($inscripcion->estado == 'PE')
                                            value="PENDIENTE"
                                        @elseif ($inscripcion->estado == 'RE')
                                            value="RECHAZADO"
                                        @elseif ($inscripcion->estado == 'AT')
                                            value="APROBADO POR TUTOR"
                                        @elseif ($inscripcion->estado == 'AC')
                                            value="APROBADO POR COORDINACION"
                                        @elseif ($inscripcion->estado == 'EC')
                                            value="ANTEPROYECTO EN CURSO"
                                        @elseif ($inscripcion->estado == 'PC')
                                            value="PROYECTO EN CURSO"
                                        @elseif ($inscripcion->estado == 'BC')
                                            value="BORRADOR EN CURSO"
                                        @elseif ($inscripcion->estado == 'AA')
                                            value="ANTEPROYECTO APROBADO"
                                        @elseif ($inscripcion->estado == 'AP')
                                            value="PROYECTO APROBADO"
                                        @elseif ($inscripcion->estado == 'AB')
                                            value="BORRADOR APROBADO"
                                        @elseif ($inscripcion->estado == 'PA')
                                            value="PAGADO"
                                        @elseif ($inscripcion->estado == 'FE')
                                            value="DEFENSA"
                                        @elseif ($inscripcion->estado == 'EN')
                                            value="APROBADO"
                                        @elseif ($inscripcion->estado == 'RR')
                                            value="REPROBADO"
                                        @endif
                                    readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control" id="tipo" value="{{$inscripcion->tipo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="area">Área</label>
                                    <input type="text" class="form-control" id="area" value="{{$inscripcion->area->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="linea">Línea</label>
                                    <input type="text" class="form-control" id="linea" value="{{$inscripcion->linea->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control" id="tutor" value="{{$inscripcion->tutor->primer_nombre}} {{$inscripcion->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="tutor_documento">N° Documento</label>
                                    <input type="text" class="form-control" id="tutor_documento" value="{{$inscripcion->tutor->numero_documento}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-7 mb-3">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$inscripcion->tema}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="lugar_investigacion">Lugar de Investigación</label>
                                    <input type="text" class="form-control" id="lugar_investigacion" value="{{$inscripcion->lugar_investigacion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_defensa">Fecha de Defensa</label>
                                    <input type="text" class="form-control text-center" id="fecha_defensa" @if ($inscripcion->fecha_defensa) value="{{\Carbon\Carbon::parse($inscripcion->fecha_defensa)->format('d/m/Y H:i')}}" @else value="-----" @endif readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="@if ($inscripcion->calificacion) col-lg-10 @else col-lg-12 @endif mb-3">
                                    <label class="form-label" for="justificacion">Importancia o Justificación</label>
                                    <textarea class="form-control" id="justificacion" cols="30" rows="5" readonly>{{$inscripcion->justificacion}}</textarea>
                                </div>
                                @if ($inscripcion->calificacion)
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="calificacion">Calificación</label>
                                        <input type="text" class="form-control" id="calificacion" value="{{$inscripcion->calificacion}}" readonly>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if ($inscripcion->rechazado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="rechazado_por">Rechazado por:</label>
                                <br>
                                {{$inscripcion->rechazadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($inscripcion->fecha_rechazo)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                        @if ($inscripcion->aprobado_tutor_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="aprobado_tutor">Aprobado por Tutor:</label>
                                <br>
                                {{$inscripcion->aprobadoTutor->name}}, en fecha: {{\Carbon\Carbon::parse($inscripcion->fecha_aprobado_tutor)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                        @if ($inscripcion->aprobado_coordinacion_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="aprobado_coordinacion">Aprobado por Coordinación:</label>
                                <br>
                                {{$inscripcion->aprobadoCoordinacion->name}}, en fecha: {{\Carbon\Carbon::parse($inscripcion->fecha_aprobado_coordinacion)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('inscripciones_temas_tesis.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('tesis.inscripciones_temas.scripts.show-scripts')
    @endsection
@endcan
