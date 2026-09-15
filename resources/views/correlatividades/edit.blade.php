{{-- @can('editar correlatividades') --}}
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
        <form action="{{route('correlatividades.update', $materia->id)}}" method="post" id="update-form">
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
                                <label class="form-label" for="materia">Materia</label>
                                <select class="form-control" id="materia" name="materia">
                                    <option value="{{$materia->id}}">{{$materia->nombre_fantasia}} - {{$materia->nombre_real}}</option>
                                </select>
                            </div>
                            <div class="col-lg-8 mb-3 text-center d-flex flex-wrap justify-content-end">
                                <div class="col-lg-3 me-3">
                                    <label class="form-label" for="cantidad_correlatividades">Cant. de Correlatividades</label>
                                    <input class="form-control text-center" type="text" id="cantidad_correlatividades" value="{{$materia->correlativas->count()}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Materias Correlativas</h4>
                            </div>
                            <div class="card-body">
                                @forelse ($materia->correlativas as $key => $detalle)
                                    <div class="mb-2 fila" id="fila-{{$key}}">
                                        <div class="row">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-materia-{{$key}}">
                                                    <label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control materia-{{$key}} materia @error('detalles.'. $key . '.materia') is-invalid @enderror" id="materia-{{$key}}" name="detalles[{{$key}}][materia]" data-live-search="true" data-live-search-normalize="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($materias as $materia)
                                                            <option value="{{$materia->id}}" @if (old('detalles.{{$key}}.materia') == strval($materia->id) || $detalle->id == strval($materia->id)) selected @endif data-subtext="{{$materia->nombre_real}}">{{$materia->nombre_fantasia}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.materia')
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
                                                <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-materia-0">
                                                    <label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control materia-0 materia @error('detalles.0.materia') is-invalid @enderror" id="materia-0" name="detalles[0][materia]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($materias as $materia)
                                                            <option value="{{$materia->id}}" @if (old('detalles.0.materia') == strval($materia->id)) selected @endif data-subtext="{{$materia->nombre_real}}">{{$materia->nombre_fantasia}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.0.materia')
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
                                <div id="materia-fila">

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
    @include('correlatividades.scripts.edit-scripts')
    @include('correlatividades.scripts.edit-detalles-scripts')
@endsection
{{-- @endcan --}}
