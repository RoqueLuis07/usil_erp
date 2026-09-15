@can('ver_convalidaciones_internas')
    @extends('layouts.master')
    @section('title') Ver Convalidación Interna @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            select[readonly] {
                pointer-events: none;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Convalidaciones Internas @endslot
            @slot('title') Ver Convalidación Interna  @endslot
        @endcomponent

        @include('convalidaciones.internas.scripts.messages-scripts')
        @include('convalidaciones.internas.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar convalidación interna</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="numero_solicitud">N° de Solicitud</label>
                                    <input type="text" class="form-control" id="numero_solicitud" value="{{$convalidacion->numero_solicitud}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$convalidacion->alumno->primer_nombre}} {{$convalidacion->alumno->primer_apellido}} - {{$convalidacion->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="universidad_origen">Universidad Origen</label>
                                    <input type="text" class="form-control" id="universidad_origen" value="{{$convalidacion->universidadOrigen->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="facultad_origen">Facultad Origen</label>
                                    <input type="text" class="form-control" id="facultad_origen" value="{{$convalidacion->facultad_origen}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="carrera_origen">Carrera Origen</label>
                                    <input type="text" class="form-control" id="carrera_origen" value="{{$convalidacion->carrera_origen}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera USIL</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$convalidacion->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="facultad">Facultad USIL</label>
                                    <input type="text" class="form-control" id="facultad" value="{{$convalidacion->facultad->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa USIL</label>
                                    <input type="text" class="form-control" id="programa" value="{{$convalidacion->programa->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Materias a Convalidar</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($convalidacion->convalidacionDetalles as $key =>$detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-5 col-lg-3 mb-2 text-center" id="div-materia_origen-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-materia_origen">Materia Origen</label> @endif
                                                    <input type="text" class="form-control" id="materia_origen-{{$key}}" value="{{$detalle->materia_origen}}">
                                                </div>
                                                <div class="col-5 col-lg-2 mb-2 text-center" id="div-calificacion_origen-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-calificacion_origen">Calificación</label> @endif
                                                    <input type="text" class="form-control text-center" id="calificacion_origen-{{$key}}" value="{{$detalle->calificacion_origen}}">
                                                </div>
                                                <div class="col-2 col-lg-2 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones">Acciones</label> @endif
                                                    <div class="align-middle" id="acciones-{{$key}}">
                                                        <a type="button" class="btn btn-icon btn-primary btn-show" id="btn-show-{{$key}}" data-bs-toggle="modal" data-bs-target="#showModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Detalles" data-id="{{$detalle->id}}"><i class="ri-eye-fill"></i></a>
                                                        @if ($convalidacion->estado != 'CO')
                                                            @if (!$detalle->numero_dictamen)
                                                                @can('dictaminar_convalidaciones_internas')
                                                                    <a type="button" class="btn btn-icon btn-info btn-dictaminar" id="btn-dictaminar-{{$detalle->id}}" data-bs-toggle="modal" data-bs-target="#dictaminarModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Dictaminar" data-id="{{$detalle->id}}" data-url="{{route('convalidaciones_internas.get_numero_dictamen', $detalle->id)}}"><i class="ri-edit-fill"></i></a>
                                                                @endcan
                                                            @elseif (!$detalle->numero_resolucion)
                                                                @can('editar_dictamen_convalidaciones_internas')
                                                                    <a type="button" class="btn btn-icon btn-warning btn-dictaminar" id="btn-dictaminar-{{$detalle->id}}" data-bs-toggle="modal" data-bs-target="#dictaminarModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar Dictamen" data-id="{{$detalle->id}}" data-url="{{route('convalidaciones_internas.get_numero_dictamen', $detalle->id)}}"><i class="ri-edit-fill"></i></a>
                                                                @endcan
                                                                @can('anular_dictamen_convalidaciones_internas')
                                                                    <a type="button" class="btn btn-icon btn-danger btn-cancel-dictamen" id="btn-cancel-dictamen-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Dictaminación" data-id="{{$detalle->id}}"><i class="ri-close-fill"></i></a>
                                                                @endcan
                                                                @can('resolucion_convalidaciones_internas')
                                                                    <a type="button" class="btn btn-icon btn-success btn-aprobar" id="btn-aprobar-{{$detalle->id}}" data-bs-toggle="modal" data-bs-target="#aprobarModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar" data-id="{{$detalle->id}}" data-url="{{route('convalidaciones_internas.get_numero_resolucion', $detalle->id)}}"><i class="ri-check-fill"></i></a>
                                                                @endcan
                                                            @elseif ($detalle->numero_resolucion)
                                                                @can('editar_resolucion_convalidaciones_internas')
                                                                    <a type="button" class="btn btn-icon btn-warning btn-aprobar" id="btn-aprobar-{{$detalle->id}}" data-bs-toggle="modal" data-bs-target="#aprobarModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar Aprobación" data-id="{{$detalle->id}}" data-url="{{route('convalidaciones_internas.get_numero_resolucion', $detalle->id)}}"><i class="ri-check-fill"></i></a>
                                                                @endcan
                                                                @can('anular_resolucion_convalidaciones_internas')
                                                                    <a type="button" class="btn btn-icon btn-danger btn-cancel-resolucion" id="btn-cancel-resolucion-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Aprobación" data-id="{{$detalle->id}}"><i class="ri-close-fill"></i></a>
                                                                @endcan
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$convalidacion->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($convalidacion->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($convalidacion->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$convalidacion->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($convalidacion->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('convalidaciones_internas.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
            @can('anular_dictamen_convalidaciones_internas')
                <form action="{{route('convalidaciones_internas.cancel_dictamen', $detalle->id)}}" method="post" id="cancel-dictamen-form-{{$detalle->id}}">
                    @csrf
                </form>
            @endcan
            @can('anular_resolucion_convalidaciones_internas')
                <form action="{{route('convalidaciones_internas.cancel_resolucion', $detalle->id)}}" method="post" id="cancel-resolucion-form-{{$detalle->id}}">
                    @csrf
                </form>
            @endcan
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('convalidaciones.internas.scripts.show-scripts')
    @endsection
@endcan
