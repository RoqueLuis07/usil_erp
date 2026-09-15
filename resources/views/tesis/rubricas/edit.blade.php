@can('editar_rubricas_tesis')
    @extends('layouts.master')
    @section('title') Editar Rúbrica de TFG @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Rúbricas de TFG @endslot
            @slot('title') Editar Rúbrica de TFG  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('rubricas_tesis.update', $rubrica->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar rúbrica de trabajos finales de grado</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre', $rubrica->nombre)}}" placeholder="Escriba el nombre">
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
                                            <option value="{{$tipo->id}}" @if (old('tipo') == strval($tipo->id) || $rubrica->tipo_id == strval($tipo->id)) selected @endif>{{$tipo->nombre}}</option>
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
                                        <input class="form-control text-center" type="text" id="puntaje_total" value="{{$rubrica->detalles->sum('puntos')}}" readonly>
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
                                    @foreach ($rubrica->detalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row">
                                                <div class="col-lg-2 mb-2 text-center" id="div-nivel-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-nivel">Pertenece a <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control nivel-{{$key}} nivel @error('detalles.'. $key . '.nivel') is-invalid @enderror" id="nivel-{{$key}}" name="detalles[{{$key}}][nivel]" data-live-search="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($niveles as $nivel)
                                                            <option value="{{$nivel->id}}" @if (old('detalles.{{$key}}.nivel') == strval($nivel->id) || $detalle->nivel_id == strval($nivel->id)) selected @endif>{{$nivel->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.nivel')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6 mb-2 me-3 text-center" id="div-descripcion-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-descripcion">Descripción <span class="text-danger">(*)</span></label> @endif
                                                    <textarea type="text" class="form-control text-center descripcion-{{$key}} descripcion @error('detalles.'. $key . '.descripcion') is-invalid @enderror" id="detalles[{{$key}}][descripcion]" name="detalles[{{$key}}][descripcion]" cols="30" rows="10" data-id="{{$key}}">{{old('detalles.' . $key . '.descripcion', $detalle->descripcion)}}</textarea>
                                                    @error('detalles.'. $key . '.descripcion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 mb-2 me-3 text-center" id="div-puntos-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-puntos">Puntos <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center puntos-{{$key}} puntos @error('detalles.'. $key . '.puntos') is-invalid @enderror" id="detalles[{{$key}}][puntos]" name="detalles[{{$key}}][puntos]" value="{{old('detalles.' . $key . '.puntos', $detalle->puntos)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.puntos')
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
                                    <div id="detalle-fila">

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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('tesis.rubricas.scripts.edit-scripts')
        @include('tesis.rubricas.scripts.edit-detalles-scripts')
    @endsection
@endcan
