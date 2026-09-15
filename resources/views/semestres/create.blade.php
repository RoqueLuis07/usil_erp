@can('crear_periodos')
    @extends('layouts.master')
    @section('title') Agregar Semestre @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Semestres @endslot
            @slot('title') Agregar Semestre  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('semestres.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo semestre</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre_semestre">Nombre <span class="text-danger">(*)</span></label>
                                    <input class="form-control @error('nombre_semestre') is-invalid @enderror" type="text" id="nombre_semestre" name="nombre_semestre" value="{{old('nombre_semestre')}}">
                                    @error('nombre_semestre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha de Inicio <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_inicio"></i>
                                        @error('fecha_inicio')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha de Fin <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_fin"></i>
                                        @error('fecha_fin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_mallas">Cant. de Carreras</label>
                                        <input class="form-control text-center" type="text" id="cantidad_mallas" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Carreras del Semestre</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-malla-0">
                                                <label class="form-label label-malla">Carrera <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control malla-0 malla @error('detalles.0.malla') is-invalid @enderror" id="malla-0" name="detalles[0][malla]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($mallas as $malla)
                                                        <option value="{{$malla->id}}" @if (old('detalles.0.malla') == strval($malla->id)) selected @endif data-subtext="{{$malla->carrera->programa->nombre}}">{{$malla->carrera->nombre_fantasia}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.malla')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-coordinador-0">
                                                <label class="form-label label-coordinador">Coordinador <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control coordinador-0 coordinador @error('detalles.0.coordinador') is-invalid @enderror" id="coordinador-0" name="detalles[0][coordinador]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($coordinadores as $coordinador)
                                                        <option value="{{$coordinador->id}}" @if (old('detalles.0.coordinador') == strval($coordinador->id)) selected @endif>{{$coordinador->primer_nombre}} {{$coordinador->primer_apellido}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.coordinador')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-fecha_inicio_matriculacion-0">
                                                <label class="form-label label-fecha_inicio_matriculacion">Inicio Matriculación <span class="text-danger">(*)</span></label>
                                                <div class="form-icon right">
                                                    <input type="text" class="flatpickr form-control form-control-icon fecha_inicio_matriculacion-0 fecha_inicio_matriculacion @error('detalles.0.fecha_inicio_matriculacion') is-invalid @enderror" id="detalles[0][fecha_inicio_matriculacion]" name="detalles[0][fecha_inicio_matriculacion]" value="{{old('detalles.0.fecha_inicio_matriculacion')}}" placeholder="Seleccionar...">
                                                    <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                                    @error('detalles.0.fecha_inicio_matriculacion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-fecha_fin_matriculacion-0">
                                                <label class="form-label label-fecha_fin_matriculacion">Fin Matriculación <span class="text-danger">(*)</span></label>
                                                <div class="form-icon right">
                                                    <input type="text" class="flatpickr form-control form-control-icon fecha_fin_matriculacion-0 fecha_fin_matriculacion @error('detalles.0.fecha_fin_matriculacion') is-invalid @enderror" id="detalles[0][fecha_fin_matriculacion]" name="detalles[0][fecha_fin_matriculacion]" value="{{old('detalles.0.fecha_fin_matriculacion')}}" placeholder="Seleccionar...">
                                                    <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                                    @error('detalles.0.fecha_fin_matriculacion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-1 col-sm-2 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="malla-fila">

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
        @include('semestres.scripts.create-scripts')
        @include('semestres.scripts.create-detalles-scripts')
    @endsection
@endcan
