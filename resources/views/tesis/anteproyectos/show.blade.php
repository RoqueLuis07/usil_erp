@can('ver_anteproyectos_tesis')
    @extends('layouts.master')
    @section('title') Ver Anteproyecto de Trabajo Final de Grado @endsection
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
            @slot('li_1') Anteproyectos de Trabajos Finales de Grado @endslot
            @slot('title') Ver Anteproyecto de Trabajo Final de Grado @endslot
        @endcomponent

        @include('tesis.anteproyectos.scripts.messages-scripts')
        @include('tesis.anteproyectos.modals.show-modals')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar anteproyecto de trabajo final de grado</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{\Carbon\Carbon::parse($tema->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$tema->alumno->primer_nombre}} {{$tema->alumno->primer_apellido}} " readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="alumno_documento">N° Documento</label>
                                    <input type="text" class="form-control" id="alumno_documento" value="{{$tema->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$tema->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$tema->programa->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold
                                        @if ($tema->estado == 'AC' || $tema->estado == 'AT' || $tema->estado == 'AC' || $tema->estado == 'AA' || $tema->estado == 'AP' || $tema->estado == 'BP' || $tema->estado == 'PA' || $tema->estado == 'EN')
                                            text-success
                                        @elseif ($tema->estado == 'EC' || $tema->estado == 'PC' || $tema->estado == 'BC' || $tema->estado == 'FE')
                                            text-warning
                                        @elseif ($tema->estado == 'RR')
                                            text-danger
                                        @endif"
                                        id="estado"
                                        @if ($tema->estado == 'AC')
                                            value="APROBADO POR COORDINACION"
                                        @elseif ($tema->estado == 'EC')
                                            value="EN CURSO"
                                        @elseif ($tema->estado == 'PC')
                                            value="PROYECTO EN CURSO"
                                        @elseif ($tema->estado == 'BC')
                                            value="BORRADOR EN CURSO"
                                        @elseif ($tema->estado == 'AA')
                                            value="APROBADO"
                                        @elseif ($tema->estado == 'AP')
                                            value="PROYECTO APROBADO"
                                        @elseif ($tema->estado == 'BP')
                                            value="BORRADOR APROBADO"
                                        @elseif ($tema->estado == 'PA')
                                            value="PAGADO"
                                        @elseif ($tema->estado == 'FE')
                                            value="DEFENSA"
                                        @elseif ($tema->estado == 'EN')
                                            value="APROBADO"
                                        @elseif ($tema->estado == 'RR')
                                            value="REPROBADO"
                                        @endif
                                    readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control" id="tipo" value="{{$tema->tipo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="area">Área</label>
                                    <input type="text" class="form-control" id="area" value="{{$tema->area->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="linea">Línea</label>
                                    <input type="text" class="form-control" id="linea" value="{{$tema->linea->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control" id="tutor" value="{{$tema->tutor->primer_nombre}} {{$tema->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="tutor_documento">N° Documento</label>
                                    <input type="text" class="form-control" id="tutor_documento" value="{{$tema->tutor->numero_documento}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$tema->tema}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="lugar_investigacion">Lugar de Investigación</label>
                                    <input type="text" class="form-control" id="lugar_investigacion" value="{{$tema->lugar_investigacion}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="justificacion">Importancia o Justificación</label>
                                    <textarea class="form-control" id="justificacion" cols="30" rows="5" readonly>{{$tema->justificacion}}</textarea>
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
                                                    <th>Alumno</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Aprobación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                @php
                                                    $anteproyecto_anterior = null;
                                                    $bandera = true;
                                                @endphp
                                                @foreach ($tema->anteproyectos as $anteproyecto)
                                                    <tr>
                                                        <td>BLOQUE {{$anteproyecto->bloque->numero}} | {{$anteproyecto->bloque->nombre}}</td>
                                                        <td>{{$anteproyecto->inscripcion->alumno->primer_nombre}} {{$anteproyecto->inscripcion->alumno->primer_apellido}}</td>
                                                        <td>
                                                            <span
                                                                class="badge @if ($anteproyecto->estado == 'PE')
                                                                    bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                                @elseif ($anteproyecto->estado == 'EN')
                                                                    bg-warning-subtle text-warning text-uppercase"> Entregado
                                                                @elseif ($anteproyecto->estado == 'CO')
                                                                    bg-warning-subtle text-warning text-uppercase"> Corregido
                                                                @elseif ($anteproyecto->estado == 'AP')
                                                                    bg-success-subtle text-success text-uppercase"> Aprobado
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($anteproyecto->fecha_aprobado_tutor)
                                                                {{\Carbon\Carbon::parse($anteproyecto->fecha_aprobado_tutor)->format('d/m/Y H:i:s')}}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @can('ver_entregas_anteproyectos_tesis')
                                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('anteproyectos_tesis.show_entregas', $anteproyecto->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Entregas"><i class="ri-eye-fill"></i></a>
                                                            @endcan
                                                            @php
                                                                if ($anteproyecto->numero_bloque > 1) {
                                                                    if ($anteproyecto_anterior->estado == 'AP') {
                                                                        $bandera = true;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if ($anteproyecto->estado != 'AP' && $bandera == true && $anteproyecto->entregas->count() > 0)
                                                                @can('aprobar_bloques_anteproyectos_tesis')
                                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{$anteproyecto->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar Bloque"><i class="ri-check-fill"></i></button>
                                                                @endcan
                                                            @elseif ($anteproyecto->estado == 'AP')
                                                                @can('anular_aprobacion_bloques_anteproyectos_tesis')
                                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#desapproveModal-{{$anteproyecto->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Aprobación Bloque"><i class="ri-close-fill"></i></button>
                                                                @endcan
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $anteproyecto_anterior = $anteproyecto;
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
                            <a type="button" class="btn btn-danger me-2" href="{{route('anteproyectos_tesis.index')}}">Volver</a>
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
        @include('tesis.anteproyectos.scripts.show-scripts')
    @endsection
@endcan
