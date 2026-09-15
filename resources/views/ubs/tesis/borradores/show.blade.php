@can('ver_borradores_tesis_ubs')
    @extends('layouts.master')
    @section('title') Ver Borrador de Tesis @endsection
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
            @slot('li_1') Borradores de Tesis @endslot
            @slot('title') Ver Borrador de Tesis @endslot
        @endcomponent

        @include('ubs.tesis.borradores.scripts.messages-scripts')
        @include('ubs.tesis.borradores.modals.show-modals')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar borrador de tesis</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
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
                                    <label class="form-label" for="programa">Llamado</label>
                                    <input type="text" class="form-control text-center" id="llamado" value="{{$tesis->curso->llamado}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="estado">Estado</label>
                                        <input type="text" class="form-control text-center fw-bold
                                            @if ($tesis->estado == 'AC' || $tesis->estado == 'AT' || $tesis->estado == 'AC' || $tesis->estado == 'AA' || $tesis->estado == 'AB' || $tesis->estado == 'PA' || $tesis->estado == 'EN')
                                                text-success
                                            @elseif ($tesis->estado == 'EC' || $tesis->estado == 'BC' || $tesis->estado == 'FE')
                                                text-warning
                                            @elseif ($tesis->estado == 'RR')
                                                text-danger
                                            @endif"
                                            id="estado"
                                            @if ($tesis->estado == 'AC')
                                                value="APROBADO POR CALIDAD"
                                            @elseif ($tesis->estado == 'EC')
                                                value="EN CURSO"
                                            @elseif ($tesis->estado == 'BC')
                                                value="BORRADOR EN CURSO"
                                            @elseif ($tesis->estado == 'AA')
                                                value="APROBADO"
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
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$tesis->tema}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="titulo">Título</label>
                                    <textarea class="form-control" id="titulo" cols="30" rows="3" readonly>{{$tesis->titulo}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Bloques</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle table-nowrap">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Bloque</th>
                                                    <th>Módulo</th>
                                                    <th>Calificación</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Aprobación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                @php
                                                    $borrador_anterior = null;
                                                    $bandera = true;
                                                @endphp
                                                @foreach ($tesis->borradores as $borrador)
                                                    <tr>
                                                        <td>BLOQUE {{$borrador->bloque->numero}} | {{$borrador->bloque->nombre}}</td>
                                                        <td>{{$borrador->modulo->nombre_fantasia}}</td>
                                                        <td>{{$borrador->calificacion}}</td>
                                                        <td>
                                                            <span
                                                                class="badge @if ($borrador->estado == 'PE')
                                                                    bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                                @elseif ($borrador->estado == 'EN')
                                                                    bg-warning-subtle text-warning text-uppercase"> Entregado
                                                                @elseif ($borrador->estado == 'CO')
                                                                    bg-warning-subtle text-warning text-uppercase"> Corregido
                                                                @elseif ($borrador->estado == 'AT')
                                                                    bg-success-subtle text-success text-uppercase"> Ap. Tutor
                                                                @elseif ($borrador->estado == 'AC')
                                                                    bg-success-subtle text-success text-uppercase"> Ap. Calidad
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($borrador->fecha_aprobado_tutor)
                                                                Tutor: {{\Carbon\Carbon::parse($borrador->fecha_aprobado_tutor)->format('d/m/Y H:i:s')}}
                                                                @if ($borrador->fecha_aprobado_calidad)
                                                                    <br>
                                                                    Calidad: {{\Carbon\Carbon::parse($borrador->fecha_aprobado_calidad)->format('d/m/Y H:i:s')}}
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @can('ver_entregas_borradores_tesis_ubs')
                                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('tesis_ubs.show_entregas_borrador', $borrador->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Entregas"><i class="ri-eye-fill"></i></a>
                                                            @endcan
                                                            @php
                                                                if ($borrador->numero_bloque > 1) {
                                                                    if ($borrador_anterior->estado == 'AC') {
                                                                        $bandera = true;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if (($borrador->estado == 'EN' || $borrador->estado == 'CO') && $bandera == true && $borrador->entregas->count() > 0)
                                                                @can('aprobar_tutor_borradores_tesis_ubs')
                                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveTutorModal-{{$borrador->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar Tutor"><i class="ri-check-fill"></i></button>
                                                                @endcan
                                                            @elseif ($borrador->estado == 'AT')
                                                                @can('anular_aprobacion_tutor_borradores_tesis_ubs')
                                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#desapproveTutorModal-{{$borrador->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Aprobación Tutor"><i class="ri-close-fill"></i></button>
                                                                @endcan
                                                                @can('aprobar_calidad_borradores_tesis_ubs')
                                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveCalidadModal-{{$borrador->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar Calidad"><i class="ri-check-fill"></i></button>
                                                                @endcan
                                                            @elseif ($borrador->estado == 'AC')
                                                                @can('anular_aprobacion_calidad_borradores_tesis_ubs')
                                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#desapproveCalidadModal-{{$borrador->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Aprobación Calidad"><i class="ri-close-fill"></i></button>
                                                                @endcan
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $borrador_anterior = $borrador;
                                                        $bandera = false;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('tesis_ubs.show', $tesis->id)}}">Volver</a>
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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.tesis.borradores.scripts.show-scripts')
    @endsection
@endcan
