@can('crear_clases_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Agregar Clase @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
        <style>
            /* Chrome, Safari, Edge */
            input[type="number"]::-webkit-outer-spin-button,
            input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Firefox */
            input[type="number"] {
                -moz-appearance: textfield;
            }

            .ck-editor__editable_inline {
                min-height: 5cm;
                font-family: 'Open-sans', sans-serif;
                font-size: 14px;
                padding: 10px;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Clases @endslot
            @slot('title') Agregar Clase @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('clases_maestrias.store', ['curso' => $curso->id, 'modulo' => $modulo->id, 'docente' => $docente->id])}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="NO">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva clase de {{$curso->nombre_fantasia}} - Módulo {{Str::title($modulo->nombre_fantasia)}}</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="curso">Maestria</label>
                                    <input type="text" class="form-control" id="curso" value="{{$curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="modulo">Módulo</label>
                                    <input type="text" class="form-control" id="modulo" value="{{$modulo->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$docente->primer_nombre}} {{$docente->primer_apellido}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Datos de la Clase</h5>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3 text-center">
                                                    <label class="form-label" for="fecha_hora">Fecha y Hora <span class="text-danger">(*)</span></label>
                                                    <div class="form-icon right">
                                                        <input type="text" class="form-control form-control-icon flatpickr text-center @error('fecha_hora') is-invalid @enderror" id="fecha_hora" name="fecha_hora" value="{{old('fecha_hora')}}">
                                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                                        @error('fecha_hora')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 mb-3 text-center">
                                                    <label class="form-label" for="tema_desarrollado">Tema Desarrollado <span class="text-danger">(*)</span></label>
                                                    <input type="text" class="form-control @error('tema_desarrollado') is-invalid @enderror" id="tema_desarrollado" name="tema_desarrollado" value="{{old('tema_desarrollado')}}">
                                                    @error('tema_desarrollado')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-3 mb-3 text-center">
                                                    <label class="form-label" for="horas_desarrollo">Horas de Desarrollo <span class="text-danger">(*)</span></label>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="input-step">
                                                            <button type="button" class="minus">-</button>
                                                            <input type="number" class="form-control @error('horas_desarrollo') is-invalid @enderror" id="horas_desarrollo" name="horas_desarrollo" value="{{old('horas_desarrollo', 1)}}" min="1" max="10">
                                                            <button type="button" class="plus">+</button>
                                                        </div>
                                                    </div>
                                                    @error('horas_desarrollo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4 mb-3 text-center">
                                                    <label class="form-label" for="modalidad">Modalidad <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($modalidades as $modalidad)
                                                            <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id) || $curso->modalidad_id == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('modalidad')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="col-lg-12 mb-3">
                                                <label class="form-label" for="observaciones">Observaciones</label>
                                                <textarea class="form-control" id="observaciones" name="observaciones" cols="30" rows="10"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('clases_maestrias.index', $curso->id)}}">Cancelar</button>
                            <button type="button" class="btn btn-success me-2" id="save-btn">Guardar</button>
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
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/ckeditor.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/translations/es.min.js"></script>
        @include('ubs.maestrias.clases.scripts.create-scripts')
    @endsection
@endcan
