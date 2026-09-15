@can('ver_entregas_anteproyectos_tesis_ubs')
    @extends('layouts.master')
    @section('title') Ver Entregas de Anteproyecto @endsection
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
            @slot('li_1') Ver Anteproyecto de Tesis @endslot
            @slot('title') Ver Entregas de Anteproyecto | Bloque {{$anteproyecto->bloque->numero}} @endslot
        @endcomponent

        @include('ubs.tesis.anteproyectos.scripts.messages-scripts')
        @include('ubs.tesis.anteproyectos.modals.show_entregas-modals')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Bloque {{$anteproyecto->bloque->numero}} | {{Str::title($anteproyecto->bloque->nombre)}}</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @if ($anteproyecto->estado != 'AC' || $anteproyecto->estado != 'AT')
                                    @can('entregar_anteproyectos_tesis_ubs')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEntregaModal" id="add-entrega-btn"><i class="ri-add-line align-middle"></i> Entrega</button>
                                    @endcan
                                    @can('corregir_anteproyectos_tesis_ubs')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCorreccionModal" id="add-correccion-btn"><i class="ri-add-line align-middle"></i> Corrección / Comentario</button>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="list-group">
                                @forelse ($anteproyecto->entregas as $entrega)
                                    <a class="list-group-item list-group-action">
                                        <div class="float-end">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#entregaModal-{{$entrega->id}}">Ver Detalles</button>
                                            @if ($anteproyecto->estado != 'AP')
                                                @can('eliminar_entregas_anteproyectos_tesis_ubs')
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEntregaModal-{{$entrega->id}}" id="delete-entrega-btn"><i class="ri-delete-bin-line"></i> Eliminar</button>
                                                @endcan
                                            @endif
                                        </div>
                                        <div class="d-flex mb-2 align-items-center">
                                            <div class="flex-shrink-0">
                                                @if ($entrega->entrega == false)
                                                    @if ($entrega->anteproyecto->inscripcion->tutor->url_ubicacion_foto)
                                                        <img class="avatar-sm rounded-circle" src="{{asset($entrega->anteproyecto->inscripcion->tutor->url_ubicacion_foto)}}" alt="Avatar">
                                                    @else
                                                        <img class="avatar-sm rounded-circle" src="{{asset('storage/usuarios/' . $entrega->anteproyecto->inscripcion->tutor->usuario->avatar)}}" alt="Avatar">
                                                    @endif
                                                @else
                                                    @if ($entrega->anteproyecto->inscripcion->alumno->url_ubicacion_foto)
                                                        <img class="avatar-sm rounded-circle" src="{{asset($entrega->anteproyecto->inscripcion->alumno->url_ubicacion_foto)}}" alt="Avatar">
                                                    @else
                                                        <img class="avatar-sm rounded-circle" src="{{asset('storage/usuarios/' . $entrega->anteproyecto->inscripcion->alumno->usuario->avatar)}}" alt="Avatar">
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="list-title fs-base mb-1">
                                                    @if ($entrega->entrega == false)
                                                        {{$entrega->anteproyecto->inscripcion->tutor->primer_nombre}} {{$entrega->anteproyecto->inscripcion->tutor->primer_apellido}} @if ($entrega->url_archivo) <span class="text-danger">contiene archivo</span> @endif
                                                    @else
                                                        {{$entrega->anteproyecto->inscripcion->alumno->primer_nombre}} {{$entrega->anteproyecto->inscripcion->alumno->primer_apellido}} @if ($entrega->url_archivo) <span class="text-danger">contiene archivo</span> @endif
                                                    @endif
                                                </h5>
                                                @php
                                                    $fecha_obtenida = \Carbon\Carbon::parse($entrega->created_at);
                                                    $fecha_actual = \Carbon\Carbon::now();
                                                    $fecha = 'hace ' . $fecha_obtenida->diffForHumans($fecha_actual, true);
                                                @endphp
                                                <p class="list-text mb-0 fs-xs">{{$fecha}}</p>
                                            </div>
                                        </div>
                                        <p class="list-text mb-0">{{$entrega->comentario}}</p>
                                    </a>
                                @empty
                                    <div class="text-center">
                                        <p>El bloque seleccionado no cuenta con ninguna entrega/corrección realizada, por favor agregue una.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)}}">Volver</a>
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
        @include('ubs.tesis.anteproyectos.scripts.show_entregas-scripts')
    @endsection
@endcan
