@can('ver_tesis_ubs')
    @extends('layouts.master')
    @section('title') Ver Tesis @endsection
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
            @slot('li_1') Tesis @endslot
            @slot('title') Ver Tesis @endslot
        @endcomponent

        @include('ubs.tesis.scripts.messages-scripts')
        @include('ubs.tesis.modals.show-modals')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar tesis</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @if ($tesis->estado == 'PE')
                                    @can('aprobar_tutor_tesis_ubs')
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#approveTutorModal">Aprobar (Tutor)</button>
                                    @endcan
                                    @can('rechazar_tesis_ubs')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Rechazar</button>
                                    @endcan
                                @elseif ($tesis->estado == 'AT')
                                    @can('aprobar_calidad_tesis_ubs')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveCalidadModal">Aprobar (Calidad)</button>
                                    @endcan
                                    @can('anular_aprobacion_tutor_tesis_ubs')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveTutorModal">Anular Aprobación (Tutor)</button>
                                    @endcan
                                @elseif ($tesis->estado == 'AC')
                                    @if ($tesis->anteproyectos->count() == 0)
                                        @can('anular_aprobacion_calidad_tesis_ubs')
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveCalidadModal">Anular Aprobación (Calidad)</button>
                                        @endcan
                                    @endif
                                @elseif ($tesis->estado == 'RE')
                                    @can('anular_rechazo_tesis_ubs')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#unrejectModal">Anular Rechazo</button>
                                    @endcan
                                @elseif ($tesis->estado == 'PA')
                                    @can('asignar_fecha_defensa_tesis_ubs')
                                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#asignarFechaDefensaModal" id="asignar-fecha-defensa-btn">Asignar Fecha de Defensa</button>
                                    @endcan
                                @elseif ($tesis->estado == 'FE')
                                    @can('generar_actas_tesis_ubs')
                                        <a type="button" class="btn btn-warning me-2" href="{{route('tesis_ubs.generate_acta', $tesis->id)}}" target="_blank" id="generar-btn">@if (!$tesis->acta_generado) Generar Acta @else Regenerar Acta @endif</a>
                                    @endcan
                                    @can('puntuar_defensas_tesis_ubs')
                                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#puntuarDefensaModal" id="puntuar-defensa-btn">Puntuar Defensa</button>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{\Carbon\Carbon::parse($tesis->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}} " readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="alumno_documento">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="alumno_documento" value="{{$tesis->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="maestria">Maestría</label>
                                    <input type="text" class="form-control" id="maestria" value="{{$tesis->curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="llamado">Llamado</label>
                                    <input type="text" class="form-control text-center" id="llamado" value="{{$tesis->curso->llamado}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold
                                        @if ($tesis->estado == 'PE' || $tesis->estado == 'RE' || $tesis->estado == 'RR')
                                            text-danger
                                        @elseif ($tesis->estado == 'AT' || $tesis->estado == 'EC' || $tesis->estado == 'BC' || $tesis->estado == 'FE')
                                            text-warning
                                        @elseif ($tesis->estado == 'AC' || $tesis->estado == 'AA' || $tesis->estado == 'AB' || $tesis->estado == 'PA' || $tesis->estado == 'EN')
                                            text-success
                                        @endif"
                                        id="estado"
                                        @if ($tesis->estado == 'PE')
                                            value="PENDIENTE"
                                        @elseif ($tesis->estado == 'RE')
                                            value="RECHAZADO"
                                        @elseif ($tesis->estado == 'AT')
                                            value="APROBADO POR TUTOR"
                                        @elseif ($tesis->estado == 'AC')
                                            value="APROBADO POR CALIDAD"
                                        @elseif ($tesis->estado == 'EC')
                                            value="ANTEPROYECTO EN CURSO"
                                        @elseif ($tesis->estado == 'BC')
                                            value="BORRADOR EN CURSO"
                                        @elseif ($tesis->estado == 'AA')
                                            value="ANTEPROYECTO APROBADO"
                                        @elseif ($tesis->estado == 'AB')
                                            value="BORRADOR APROBADO"
                                        @elseif ($tesis->estado == 'PA')
                                            value="PAGADO"
                                        @elseif ($tesis->estado == 'FE')
                                            value="DEFENSA"
                                        @elseif ($tesis->estado == 'EN')
                                            value="APROBADO"
                                        @elseif ($tesis->estado == 'RR')
                                            value="REPROBADO"
                                        @endif
                                    readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="linea">Línea</label>
                                    <input type="text" class="form-control" id="linea" value="{{$tesis->linea->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control" id="tutor" value="{{$tesis->tutor->primer_nombre}} {{$tesis->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="tutor_documento">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="tutor_documento" value="{{$tesis->tutor->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="pagado">Pagado</label>
                                    <input type="radio" class="btn-check" id="pagado" @if ($tesis->pagado == 'CA') checked @endif disabled>
                                    <label class="btn @if ($tesis->pagado == 'PE') btn-outline-danger @elseif ($tesis->pagado == 'PA') btn-outline-warning @elseif ($tesis->pagado == 'CA') btn-outline-success @endif" for="pagado">@if ($tesis->pagado == 'PE') Pendiente @elseif ($tesis->pagado == 'PA') Parcial @elseif ($tesis->pagado == 'CA') Pagado @endif</label>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_pago">Fecha Pago</label>
                                    <input type="text" class="form-control text-center" id="fecha_pago" value="{{\Carbon\Carbon::parse($tesis->fecha_pago)->format('d/m/Y H:i')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$tesis->tema}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_defensa">Fecha de Defensa</label>
                                    <input type="text" class="form-control text-center" id="fecha_defensa" @if ($tesis->fecha_defensa_id) value="{{\Carbon\Carbon::parse($tesis->fechaDefensa->fecha)->format('d/m/Y')}} {{$tesis->fechaDefensa->hora}}" @else value="-----" @endif readonly>
                                </div>
                                @if ($tesis->calificacion)
                                    <div class="col-lg-1 mb-3">
                                        <label class="form-label" for="calificacion">Calificación</label>
                                        <input type="text" class="form-control text-center" id="calificacion" @if ($tesis->calificacion) value="{{number_format($tesis->calificacion, 2, ',', '.')}}" @else value="N/A" @endif readonly>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="titulo">Título</label>
                                    <textarea class="form-control" id="titulo" cols="30" rows="5" readonly>{{$tesis->titulo}}</textarea>
                                </div>
                            </div>
                            @if ($tesis->estado == 'EC' || $tesis->estado == 'AC' || $tesis->estado == 'AA' || $tesis->estado == 'BC' || $tesis->estado == 'AB' || $tesis->estado == 'PA' || $tesis->estado == 'FE' || $tesis->estado == 'EN' || $tesis->estado == 'RR')
                                <div class="row d-flex flex-wrap justify-content-center">
                                    @can('crear_anteproyectos_tesis_ubs')
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="anteproyecto">Anteproyecto</label>
                                            <div>
                                                @if ($tesis->anteproyectos->count() != 0)
                                                    <a class="btn btn-info" href="{{route('tesis_ubs.show_anteproyecto', $tesis->id)}}">Ver Anteproyecto</a>
                                                @else
                                                    <a class="btn btn-warning" href="{{route('tesis_ubs.create_anteproyecto', $tesis->id)}}">Generar Anteproyecto</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endcan
                                    @if ($tesis->estado == 'AA' || $tesis->estado == 'BC' || $tesis->estado == 'AB' || $tesis->estado == 'PA' || $tesis->estado == 'FE' || $tesis->estado == 'EN' || $tesis->estado == 'RR')
                                        @can('crear_borradores_tesis_ubs')
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label" for="borrador">Borrador</label>
                                                <div>
                                                    @if ($tesis->borradores->count() != 0)
                                                        <a class="btn btn-info" href="{{route('tesis_ubs.show_borrador', $tesis->id)}}">Ver Borrador</a>
                                                    @else
                                                        <a class="btn btn-warning" href="{{route('tesis_ubs.create_borrador', $tesis->id)}}">Generar Borrador</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endcan
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        @if ($tesis->rechazado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="rechazado_por">Rechazado por:</label>
                                <br>
                                {{$tesis->rechazadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($tesis->fecha_rechazo)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                        @if ($tesis->aprobado_tutor_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="aprobado_tutor">Aprobado por Tutor:</label>
                                <br>
                                {{$tesis->aprobadoTutor->name}}, en fecha: {{\Carbon\Carbon::parse($tesis->fecha_aprobado_tutor)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                        @if ($tesis->aprobado_calidad_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="aprobado_calidad">Aprobado por Calidad Educativa:</label>
                                <br>
                                {{$tesis->aprobadoCalidad->name}}, en fecha: {{\Carbon\Carbon::parse($tesis->fecha_aprobado_calidad)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('tesis_ubs.index')}}">Volver</a>
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
        @include('ubs.tesis.scripts.show-scripts')
    @endsection
@endcan
