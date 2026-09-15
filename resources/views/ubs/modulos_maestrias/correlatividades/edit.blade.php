@can('editar_correlatividades_modulos_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Editar Correlatividad @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Correlatividades @endslot
            @slot('title') Editar Correlatividad  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('modulos_correlatividades.update', $modulo->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar correlatividad</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="modulo">Módulo</label>
                                    <select class="form-control" id="modulo" name="modulo">
                                        <option value="{{$modulo->id}}">{{$modulo->nombre_fantasia}} - {{$modulo->nombre_real}}</option>
                                    </select>
                                </div>
                                <div class="col-lg-8 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_correlatividades">Cant. de Correlatividades</label>
                                        <input class="form-control text-center" type="text" id="cantidad_correlatividades" value="{{$modulo->correlativas->count()}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Módulos Correlativas</h4>
                                </div>
                                <div class="card-body">
                                    @forelse ($modulo->correlativas as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row">
                                                <div class="d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-modulo-{{$key}}">
                                                        <label class="form-label label-modulo">Módulo <span class="text-danger">(*)</span></label>
                                                        <select class="selectpicker form-control modulo-{{$key}} modulo @error('detalles.'. $key . '.modulo') is-invalid @enderror" id="modulo-{{$key}}" name="detalles[{{$key}}][modulo]" data-live-search="true" data-live-search-normalize="true" data-id="{{$key}}">
                                                            <option value="" selected disabled>Seleccionar...</option>
                                                            @foreach ($modulos as $modulo)
                                                                <option value="{{$modulo->id}}" @if (old('detalles.{{$key}}.modulo') == strval($modulo->id) || $detalle->id == strval($modulo->id)) selected @endif data-subtext="{{$modulo->nombre_real}}">{{$modulo->nombre_fantasia}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('detalles.'. $key . '.modulo')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-2 col-sm-2 text-center">
                                                        <label class="form-label label-acciones">Acciones</label>
                                                        <div class="align-middle" id="acciones-{{$key}}">
                                                            @if ($key == 0 && !$loop->last)
                                                                <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- primero y no ultimo --}}
                                                            @elseif ($key > 0 && !$loop->last)
                                                                <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button>  {{-- no primero y no ultimo --}}
                                                            @elseif ($key > 0 && $loop->last)
                                                                <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- no primero y ultimo --}}
                                                                <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="{{$key}}"><i class="ri-add-fill"></i></button>
                                                            @elseif ($key == 0 && $loop->last)
                                                                <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="{{$key}}"><i class="ri-add-fill"></i></button> {{-- primero y ultimo --}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="mb-2 fila" id="fila-0">
                                            <div class="row">
                                                <div class="d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-modulo-0">
                                                        <label class="form-label label-modulo">Módulo <span class="text-danger">(*)</span></label>
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
                                                    </div>
                                                    <div class="col-lg-2 col-sm-2 text-center">
                                                        <label class="form-label label-acciones">Acciones</label>
                                                        <div class="align-middle" id="acciones-0">
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                        </div>
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
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <a type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</a>
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
        @include('ubs.modulos_maestrias.correlatividades.scripts.edit-scripts')
        @include('ubs.modulos_maestrias.correlatividades.scripts.edit-detalles-scripts')
    @endsection
@endcan
