@can('editar_convalidaciones_externas')
    @extends('layouts.master')
    @section('title') Editar Convalidación Externa @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Convalidacion Externas @endslot
            @slot('title') Editar Convalidación Externa  @endslot
        @endcomponent

        @include('convalidaciones.externas.modals.edit-modals')

        <div class="row">
            <form action="{{route('convalidaciones_externas.update', $convalidacion->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar convalidación externa</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
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
                                        <select class="selectpicker form-control @error('universidad_origen') is-invalid @enderror" id="universidad_origen" name="universidad_origen" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($instituciones_educativas as $institucion_educativa)
                                                <option value="{{$institucion_educativa->id}}" @if (old('universidad_origen') == strval($institucion_educativa->id) || $convalidacion->universidad_origen_id == strval($institucion_educativa->id)) selected @endif>{{$institucion_educativa->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @error('univesidad_origen')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="facultad_origen">Facultad Origen</label>
                                        <input type="text" class="form-control @error('facultad_origen') is-invalid @enderror" id="facultad_origen" name="facultad_origen" value="{{$convalidacion->facultad_origen}}">
                                        @error('facultad_origen')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="carrera_origen">Carrera Origen</label>
                                        <input type="text" class="form-control @error('carrera_origen') is-invalid @enderror" id="carrera_origen" name="carrera_origen" value="{{$convalidacion->carrera_origen}}">
                                        @error('carrera_origen')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="carrera">Carrera USIL</span></label>
                                        <input type="text" class="form-control" id="carrera" name="carrera" value="{{$convalidacion->carrera->nombre_fantasia}}" readonly>
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="facultad">Facultad</label>
                                        <input type="text" class="form-control" id="facultad" value="{{$convalidacion->carrera->facultad->nombre}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="programa">Programa</label>
                                        <input type="text" class="form-control" id="programa" value="{{$convalidacion->carrera->programa->nombre}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3" id="div-certificado">
                                        <label class="form-label" for="certificado">Certificado de Estudios <span class="text-danger">(*)</span></label>
                                        <div class="d-flex justify-content-center">
                                            <a type="button" class="btn btn-info me-2" href="{{asset($convalidacion->ubicacion_certificado_estudios)}}" target="_blank" id="btn-show-certificado">Ver Certificado</a>
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#certificadoModal">Cambiar Certificado</button>
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
                                    <h4 class="card-title mb-0">Materias a Convalidar</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($convalidacion->convalidacionDetalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-5 col-lg-3 mb-2 text-center" id="div-materia_origen-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-materia_origen" for="materia_origen-{{$key}}">Materia <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control materia_origen-{{$key}} materia_origen @error('detalles.' . $key . '.materia_origen') is-invalid @enderror" id="materia_origen-{{$key}}" name="detalles[{{$key}}][materia_origen]" value="{{old('detalles.' . $key . '.materia_origen', $detalle->materia_origen)}}">
                                                    @error('detalles.' . $key . '.materia_origen')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-5 col-lg-2 mb-2 text-center" id="div-calificacion_origen-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-calificacion_origen" for="calificacion_origen-{{$key}}">Calificación <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center calificacion_origen-{{$key}} calificacion_origen @error('detalles.' . $key . '.calificacion_origen') is-invalid @enderror" id="calificacion_origen-{{$key}}" name="detalles[{{$key}}][calificacion_origen]" value="{{old('detalles.' . $key . '.calificacion_origen', $detalle->calificacion_origen)}}">
                                                </div>
                                                <div class="col-1 col-lg-1 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones" >Acciones</label> @endif
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
                                    @endforeach
                                    <div id="materia-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('convalidaciones_externas.index')}}">Cancelar</a>
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
        @include('convalidaciones.externas.scripts.edit-scripts')
        @include('convalidaciones.externas.scripts.edit-detalles-scripts')
    @endsection
@endcan
