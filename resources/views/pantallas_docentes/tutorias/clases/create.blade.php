@can('crear_clases_tutorias_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Agregar Clase de Tutoría @endsection
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
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('tutorias_clases.store', $tutoria->id)}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="SI">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva clase de tutoría</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$tutoria->materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$tutoria->docente->primer_nombre}} {{$tutoria->docente->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$tutoria->semestre->nombre}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Datos de la Clase</h5>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
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
                                                            <input type="number" class="form-control @error('horas_desarrollo') is-invalid @enderror" id="horas_desarrollo" name="horas_desarrollo" value="{{old('horas_desarrollo', $cantidad_horas)}}" min="1" max="10">
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
                                                    <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($modalidades as $modalidad)
                                                            <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id) || $tutoria->modalidad_id == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('modalidad')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-8 mb-3">
                                                    <label class="form-label" for="observaciones">Observaciones</label>
                                                    <textarea class="form-control" id="observaciones" name="observaciones" cols="30" rows="10"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Lista de Asistencia</h5>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            @foreach ($tutoria->alumnos as $key => $detalle)
                                                <div class="row">
                                                    <div class="col-lg-3 mb-2 text-center">
                                                        @if ($key == 0) <label class="form-label" for="numero_documento-{{$key}}">N° Documento</label> @endif
                                                        <input type="text" class="form-control text-center" id="numero_documento-{{$key}}" value="{{number_format($detalle->alumno->numero_documento, 0, ',', '.')}}" readonly>
                                                    </div>
                                                    <div class="col-lg-5 mb-2 text-center">
                                                        @if ($key == 0) <label class="form-label" for="alumno-{{$key}}">Alumno</label> @endif
                                                        <input type="text" class="form-control @error('asistencias.' . $key . '.alumno') is-invalid @enderror" id="alumno-{{$key}}" value="{{$detalle->alumno->primer_apellido}}, {{$detalle->alumno->primer_nombre}}" readonly>
                                                        <input type="hidden" name="asistencias[{{$key}}][alumno]" value="{{$detalle->alumno_id}}">
                                                        @error('asistencias.' . $key . '.alumno')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-2 mb-2 text-center">
                                                        @if ($key == 0) <label class="form-label" for="asistencia-{{$key}}">Asistencia <span class="text-danger">(*)</span></label> @endif
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-outline-danger ausente-presente-btn" data-id="{{$key}}"><span class="ausente-presente-btn-text-{{$key}}">Ausente</span></button>
                                                            <input type="hidden" id="asistencia-{{$key}}" name="asistencias[{{$key}}][estado]" value="AU">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 mb-2 text-center">
                                                        @if ($key == 0) <label class="form-label" for="observaciones-{{$key}}">Observaciones</label> @endif
                                                        <input type="text" class="form-control" id="observaciones-{{$key}}" name="asistencias[{{$key}}][observaciones]" value="{{old('observaciones')}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-id="{{$tutoria->id}}">Cancelar</button>
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
        @include('pantallas_docentes.tutorias.clases.scripts.create-scripts')
    @endsection
@endcan
