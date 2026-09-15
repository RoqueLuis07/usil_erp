@can('editar_escalas')
    @extends('layouts.master')
    @section('title') Editar Escala @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Escalas @endslot
            @slot('title') Editar Escala  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('escalas.update', $escala->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar escala</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre', $escala->nombre)}}">
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
                                            <option value="{{$programa->id}}" @if (old('programa') == strval($programa->id) || $escala->programa_id == strval($programa->id)) selected @endif>{{$programa->nombre}}</option>
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
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Escala</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($escala->escalaDetalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-punto_minimo-{{$key}}">
                                                    @if ($key == 0)<label class="form-label label-punto_minimo">Puntaje Mínimo <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center punto_minimo-{{$key}} punto_minimo @error('detalles.'. $key . '.punto_minimo') is-invalid @enderror" id="detalles[{{$key}}][punto_minimo]" name="detalles[{{$key}}][punto_minimo]" value="{{old('detalles.' . $key . '.punto_minimo', $detalle->punto_minimo)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.punto_minimo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-punto_maximo-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-punto_maximo">Puntaje Máximo <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center punto_maximo-{{$key}} punto_maximo @error('detalles.'. $key . '.punto_maximo') is-invalid @enderror" id="detalles[{{$key}}][punto_maximo]" name="detalles[{{$key}}][punto_maximo]" value="{{old('detalles.' . $key . '.punto_maximo', $detalle->punto_maximo)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.punto_maximo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-nota-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-nota">Nota <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center nota-{{$key}} nota @error('detalles.'. $key . '.nota') is-invalid @enderror" id="detalles[{{$key}}][nota]" name="detalles[{{$key}}][nota]" value="{{old('detalles.' . $key . '.nota', $detalle->nota)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.nota')
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
                                    @endforeach
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('escalas.scripts.edit-scripts')
        @include('escalas.scripts.edit-detalles-scripts')
    @endsection
@endcan
