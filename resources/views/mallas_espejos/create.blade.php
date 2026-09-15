@can('crear_mallas_espejo')
    @extends('layouts.master')
    @section('title') Agregar Malla Espejo @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Mallas Espejo @endslot
            @slot('title') Agregar Malla Espejo  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('mallas_espejos.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva malla espejo</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row d-flex justify-content-center">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="malla_paraguay">Malla Paraguay <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('malla_paraguay') is-invalid @enderror" id="malla_paraguay" name="malla_paraguay" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($mallas_paraguay as $malla_paraguay)
                                            <option value="{{$malla_paraguay->id}}" @if (old('malla_paraguay') == strval($malla_paraguay->id)) selected @endif data-subtext="{{$malla_paraguay->carrera->programa->nombre}}">{{$malla_paraguay->carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('malla_paraguay')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="malla_siu">Malla SIU <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('malla_siu') is-invalid @enderror" id="malla_siu" name="malla_siu" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($mallas_siu as $malla_siu)
                                            <option value="{{$malla_siu->id}}" @if (old('malla_siu') == strval($malla_siu->id)) selected @endif data-subtext="{{$malla_siu->carrera->programa->nombre}}">{{$malla_siu->carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('malla_siu')
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
                                    <h4 class="card-title mb-0">Materias de la Malla Espejo</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-6 col-lg-3 mb-2 text-center" id="div-materia_paraguay-0">
                                                <label class="form-label label-materia_paraguay">Materia Paraguay <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control materia_paraguay-0 materia_paraguay @error('detalles.0.materia_paraguay') is-invalid @enderror" id="materia_paraguay-0" name="detalles[0][materia_paraguay]" data-live-search="true" data-live-search-normalize="true" data-id="0" disabled>
                                                    <option value="" selected disabled>Seleccionar...</option>

                                                </select>
                                                @error('detalles.0.materia_paraguay')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-5 col-lg-3 mb-2 text-center" id="div-materia_siu-0">
                                                <label class="form-label label-materia_siu">Materia SIU <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control materia_siu-0 materia_siu @error('detalles.0.materia_siu') is-invalid @enderror" id="materia_siu-0" name="detalles[0][materia_siu]" data-live-search="true" data-live-search-normalize="true" data-id="0" disabled>
                                                    <option value="" selected disabled>Seleccionar...</option>

                                                </select>
                                                @error('detalles.0.materia_siu')
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
        @include('mallas_espejos.scripts.create-scripts')
        @include('mallas_espejos.scripts.create-detalles-scripts')
    @endsection
@endcan
