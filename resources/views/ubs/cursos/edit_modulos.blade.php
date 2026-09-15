@can('editar_modulos_cursos_ubs')
    @extends('layouts.master')
    @section('title') @if ($curso->modulos->count() > 0) Editar Módulos del Curso @else Agregar Módulos al Curso @endif @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Cursos @endslot
            @slot('title') @if ($curso->modulos->count() > 0) Editar Módulos del Curso @else Agregar Módulos al Curso @endif @endslot
        @endcomponent

        @include('ubs.cursos.modals.edit_modulos-modals')

        <div class="row">
            <form action="{{route('cursos.update_modulos', $curso->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">@if ($curso->modulos->count() > 0) Actualizar módulos del curso @else Agregar módulos al curso @endif </h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="nombre_fantasia">Nombre Fantasía</label>
                                        <input type="text" class="form-control" id="nombre_fantasia" value="{{$curso->nombre_fantasia}}" readonly>
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="nombre_real">Nombre Real</label>
                                        <input type="text" class="form-control" id="nombre_real" value="{{$curso->nombre_real}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="tipo_curso">Tipo de Curso</label>
                                        <input type="text" class="form-control" id="tipo_curso" value="{{$curso->tipoCurso->nombre}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="modalidad">Modalidad</label>
                                        <input type="text" class="form-control" id="modalidad" value="{{$curso->modalidad->nombre}}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_apertura">Fecha de Apertura</label>
                                        <input type="text" class="form-control text-center" id="fecha_apertura" value="{{Carbon\Carbon::parse($curso->fecha_apertura)->format('d/m/Y')}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_fin">Fecha de Fin</label>
                                        <input type="text" class="form-control text-center" id="fecha_fin" value="{{Carbon\Carbon::parse($curso->fecha_fin)->format('d/m/Y')}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="cantidad_horas">Cantidad de Horas</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center" id="cantidad_horas" value="{{number_format($curso->cantidad_horas, 0, ',', '.')}}" readonly>
                                            <span class="input-group-text">horas</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Módulos del Curso</h4>
                                    <input type="hidden" name="nuevo" @if ($curso->modulos->count() > 0) value="NO" @else value="SI" @endif>
                                </div>
                                <div class="card-body">
                                    @forelse ($curso->modulos as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-modulo-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-modulo">Módulo <span class="text-danger">(*)</span></label> @endif
                                                    <div class="d-flex flex-row bd-highlight">
                                                        <select class="selectpicker form-control modulo-{{$key}} modulo @error('detalles.'. $key . '.modulo') is-invalid @enderror" id="modulo-{{$key}}" name="detalles[{{$key}}][modulo]" data-live-search="true" data-live-search-normalize="true" data-id="{{$key}}">
                                                            <option value="" selected disabled>Seleccionar...</option>
                                                            @foreach ($modulos as $modulo)
                                                                <option value="{{$modulo->id}}" @if (old('detalles.{{$key}}.modulo') == strval($modulo->id) || $detalle->modulo_id == strval($modulo->id)) selected @endif data-subtext="{{$modulo->nombre_real}}">{{$modulo->nombre_fantasia}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('detalles.'. $key . '.modulo')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                        <button type="button" class="btn btn-sm btn-outline-success add-modulo" data-bs-toggle="modal" data-bs-target="#createModuloModal" data-id="{{$key}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center" id="div-docente-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-docente">Docente <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control docente-{{$key}} docente @error('detalles.'. $key . '.docente') is-invalid @enderror" id="docente-{{$key}}" name="detalles[{{$key}}][docente]" data-live-search="true" data-live-search-normalize="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($docentes as $docente)
                                                            <option value="{{$docente->id}}" @if (old('detalles.{{$key}}.docente') == strval($docente->id) || $detalle->docente_id == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.docente')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 col-sm-2 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones">Acciones</label> @endif
                                                    <div class="align-middle" id="acciones-{{$key}}">
                                                        @if ($key == 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- primero y no ultimo --}}
                                                        @elseif ($key > 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button>  {{-- no primero y no ultimo --}}
                                                        @elseif ($key > 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- no primero y ultimo --}}
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button>
                                                        @elseif ($key == 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button> {{-- primero y ultimo --}}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="mb-2 fila" id="fila-0">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-modulo-0">
                                                    <label class="form-label label-modulo">Módulo <span class="text-danger">(*)</span></label>
                                                    <div class="d-flex flex-row bd-highlight">
                                                        <select class="selectpicker form-control modulo-0 modulo @error('detalles.0.modulo') is-invalid @enderror" id="modulo-0" name="detalles[0][modulo]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                            <option value="" selected disabled>Seleccionar...</option>
                                                            @foreach ($modulos as $modulo)
                                                                <option value="{{$modulo->id}}" @if (old('detalles.0.modulo') == strval($modulo->id)) selected @endif data-subtext="{{$modulo->nombre_real}}">{{$modulo->nombre_fantasia}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('detalles.0.modulo')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                        <button type="button" class="btn btn-sm btn-outline-success add-modulo" data-bs-toggle="modal" data-bs-target="#createModuloModal" data-id="0"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center" id="div-docente-0">
                                                    <label class="form-label label-docente">Docente <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control docente-0 docente @error('detalles.0.docente') is-invalid @enderror" id="docente-0" name="detalles[0][docente]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($docentes as $docente)
                                                            <option value="{{$docente->id}}" @if (old('detalles.0.docente') == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.0.docente')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 col-sm-2 text-center">
                                                    <label class="form-label label-acciones">Acciones</label>
                                                    <div class="align-middle" id="acciones-0">
                                                        <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                    <div id="modulo-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
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
        @include('ubs.cursos.scripts.edit_modulos-scripts')
    @endsection
@endcan
