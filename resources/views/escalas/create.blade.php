@can('crear_escalas')
    @extends('layouts.master')
    @section('title') Agregar Escala @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Escalas @endslot
            @slot('title') Agregar Escala  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('escalas.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva escala</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre')}}">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="programa">Programa <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('programa') is-invalid @enderror" id="programa" name="programa" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{$programa->id}}" @if (old('programa') == strval($programa->id)) selected @endif>{{$programa->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('programa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('detalles') || $errors->has('detalles'))
                        <div class="alert alert-danger">
                            <ul id="lista">
                                @foreach ($errors->all() as $error)
                                    @if (strpos($error, 'Los puntajes no pueden solaparse entre sí.') !== false)
                                        <li id="li-alert">{{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Escala</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-punto_minimo-0">
                                                <label class="form-label label-punto_minimo">Puntaje Mínimo <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center punto_minimo-0 punto_minimo @error('detalles.0.punto_minimo') is-invalid @enderror" id="detalles[0][punto_minimo]" name="detalles[0][punto_minimo]" value="{{old('detalles.0.punto_minimo')}}" data-id="0">
                                                @error('detalles.0.punto_minimo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-punto_maximo-0">
                                                <label class="form-label label-punto_maximo">Puntaje Máximo <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center punto_maximo-0 punto_maximo @error('detalles.0.punto_maximo') is-invalid @enderror" id="detalles[0][punto_maximo]" name="detalles[0][punto_maximo]" value="{{old('detalles.0.punto_maximo')}}" data-id="0">
                                                @error('detalles.0.punto_maximo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-nota-0">
                                                <label class="form-label label-nota">Nota <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center nota-0 nota @error('detalles.0.nota') is-invalid @enderror" id="detalles[0][nota]" name="detalles[0][nota]" value="{{old('detalles.0.nota')}}" data-id="0">
                                                @error('detalles.0.nota')
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
                                    <div id="puntaje-fila">

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
        @include('escalas.scripts.create-scripts')
        @include('escalas.scripts.create-detalles-scripts')
    @endsection
@endcan
