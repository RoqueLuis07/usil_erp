@can('crear_asistencias_cursos_ubs')
    @extends('layouts.master')
    @section('title') Agregar Asistencia @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Asistencias @endslot
            @slot('title') Agregar Asistencia  @endslot
        @endcomponent

        @include('ubs.cursos.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('alumnos_asistencias_ubs.store', ['curso' => $curso->curso_id, 'modulo' => $curso->modulo_id, 'docente' => $curso->docente_id])}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="NO">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva asistencia</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="curso">Curso</label>
                                    <input type="text" class="form-control" id="curso" value="{{$curso->curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="modulo">Módulo</label>
                                    <input type="text" class="form-control" id="modulo" value="{{$curso->modulo->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$curso->docente->primer_nombre}} {{$curso->docente->primer_apellido}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Modalidad <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($modalidades as $modalidad)
                                            <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id) || $curso->curso->modalidad_id == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('fecha')
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
                                    <h5 class="card-title">Lista de alumnos</h5>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                        @foreach ($alumnos as $key => $alumno)
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-8 col-lg-3 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control" id="alumno" value="{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}" readonly>
                                                    <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$alumno->id}}">
                                                </div>
                                                <div class="col-lg-2 mb-3 text-center d-none d-lg-block">
                                                    @if ($key == 0) <label class="form-label" for="ci">C.I. N°</label> @endif
                                                    <input type="text" class="form-control text-center" id="ci" value="{{$alumno->numero_documento}}" readonly>
                                                </div>
                                                <div class="col-4 col-lg-1 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="asistencia-{{$key}}">Asistencia</label> @endif
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-outline-danger ausente-presente-btn" data-id={{$key}}><span class="ausente-presente-btn-text-{{$key}}">Ausente</span></button>
                                                        <input type="hidden" id="asistencia-{{$key}}" name="detalles[{{$key}}][estado]" value="AU">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-3 mb-3 text-center">
                                                    <label class="form-label d-none @if ($key == 0) d-lg-block @endif" for="detalles[{{$key}}][observaciones]">Observaciones</label>
                                                    <input type="text" class="form-control" id="detalles[{{$key}}][observaciones]" name="detalles[{{$key}}][observaciones]" placeholder="Observaciones">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        @include('ubs.cursos.alumnos_asistencias.scripts.create-scripts')
    @endsection
@endcan
