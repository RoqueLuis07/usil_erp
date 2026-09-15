@can('crear_convalidaciones_internas')
    @extends('layouts.master')
    @section('title') Agregar Convalidación Interna @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Convalidaciones Internas @endslot
            @slot('title') Agregar Convalidación Interna  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('convalidaciones_internas.store')}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva convalidación interna</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="numero_solicitud">N° de Solicitud</label>
                                    <input type="text" class="form-control" id="numero_solicitud" name="numero_solicitud" value="{{old('numero_solicitud', $solicitud_nueva)}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('alumno') is-invalid @enderror" id="alumno" name="alumno" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($alumnos as $alumno)
                                            <option value="{{$alumno->id}}" @if (old('alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="universidad_origen">Universidad Origen</label>
                                    <input type="text" class="form-control" id="universidad_origen" value="{{$usil}}">
                                    <input type="hidden" name="universidad_origen" value="434">
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="carrera_origen">Carrera Origen <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('carrera_origen') is-invalid @enderror" id="carrera_origen" name="carrera_origen" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($carreras as $carrera)
                                            <option value="{{$carrera->id}}" @if (old('carrera_origen') == strval($carrera->id)) selected @endif data-subtext="{{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('carrera_origen')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="facultad_origen">Facultad Origen</label>
                                    <input type="text" class="form-control" id="facultad_origen" name="facultad_origen" value="{{old('facultad_origen')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera a Convalidar</label>
                                    <input type="text" class="form-control" id="carrera" name="carrera" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="facultad">Facultad</label>
                                    <input type="text" class="form-control" id="facultad" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" readonly>
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
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-5 col-lg-3 mb-2 me-3 text-center" id="div-materia_origen-0">
                                                <label class="form-label label-materia_origen">Materia Origen <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control materia_origen-0 materia_origen @error('detalles.0.materia_origen') is-invalid @enderror" id="materia_origen-0" name="detalles[0][materia_origen]" data-live-search="true" data-id="0" disabled>
                                                    <option value="" selected disabled>Seleccionar...</option>

                                                </select>
                                                @error('detalles.0.materia_origen')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-5 col-lg-2 mb-2 text-center" id="div-calificacion_origen-0">
                                                <label class="form-label label-calificacion_origen">Calificación <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center calificacion_origen-0 calificacion_origen @error('detalles.0.calificacion_origen') is-invalid @enderror" id="calificacion_origen-0" name="detalles[0][calificacion_origen]" value="{{old('detalles.0.calificacion_origen')}}" readonly>
                                                @error('detalles.0.calificacion_origen')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-1 col-lg-1 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="materia-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('convalidaciones.internas.scripts.create-scripts')
        @include('convalidaciones.internas.scripts.create-detalles-scripts')
    @endsection
@endcan
