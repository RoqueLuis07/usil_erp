@can('crear_rubricas_tesis')
    @extends('layouts.master')
    @section('title') Agregar Rúbrica de TFG @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Rúbricas de TFG @endslot
            @slot('title') Agregar Rúbrica de TFG  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('rubricas_tesis.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva rúbrica de trabajo final de grado</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre')}}" placeholder="Escriba el nombre">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tipo">Tipo de TFG <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{$tipo->id}}" @if (old('tipo') == strval($tipo->id)) selected @endif>{{$tipo->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-5 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="puntaje_total">Puntaje Total</label>
                                        <input class="form-control text-center" type="text" id="puntaje_total" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Rúbrica</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-2 mb-2 text-center" id="div-nivel-0">
                                                <label class="form-label label-nivel" for="nivel-0">Pertenece a <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker nivel-0 nivel @error('detalles.0.nivel') is-invalid @enderror" id="nivel-0" name="detalles[0][nivel]" data-live-search="true">
                                                    <option value="" disabled selected>Seleccionar...</option>
                                                    @foreach ($niveles as $nivel)
                                                        <option value="{{$nivel->id}}" @if (old('detalles.0.nivel') == strval($nivel->id)) selected @endif>{{$nivel->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.nivel')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6 mb-2 text-center" id="div-descripcion-0">
                                                <label class="form-label label-descripcion" for="descripcion-0">Descripción <span class="text-danger">(*)</span></label>
                                                <textarea class="form-control descripcion-0 descripcion @error('detalles.0.descripcion') is-invalid @enderror" id="descripcion-0" name="detalles[0][descripcion]" cols="30" rows="10">{{old('detalles.0.descripcion')}}</textarea>
                                                @error('detalles.0.descripcion')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-2 me-3 text-center" id="div-puntos-0">
                                                <label class="form-label label-puntos">Puntos <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center puntos-0 puntos @error('detalles.0.puntos') is-invalid @enderror" id="detalles[0][puntos]" name="detalles[0][puntos]" value="{{old('detalles.0.puntos')}}" data-id="0">
                                                @error('detalles.0.puntos')
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
                                    <div id="detalle-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        @include('tesis.rubricas.scripts.create-scripts')
        @include('tesis.rubricas.scripts.create-detalles-scripts')
    @endsection
@endcan
