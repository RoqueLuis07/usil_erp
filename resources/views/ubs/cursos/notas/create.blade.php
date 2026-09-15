@can('crear_notas_cursos_ubs')
    @extends('layouts.master')
    @section('title') Agregar Puntajes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Notas del Curso @endslot
            @slot('title') Agregar Puntajes  @endslot
        @endcomponent

        @include('ubs.cursos.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('cursos_notas_ubs.store', $curso->id)}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="NO">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo puntaje</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="curso">Curso</label>
                                    <input type="text" class="form-control" id="curso" value="{{$curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="llamado">N° de Llamado</label>
                                    <input type="text" class="form-control text-center" id="llamado" value="{{$curso->llamado}}" readonly>
                                </div>
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
                                        @foreach ($curso->inscripciones as $key => $inscripcion)
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-8 col-lg-3 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control" id="alumno" value="{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}" readonly>
                                                    <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$inscripcion->alumno->id}}">
                                                </div>
                                                <div class="col-lg-2 mb-3 text-center d-none d-lg-block">
                                                    @if ($key == 0) <label class="form-label" for="ci">C.I. N°</label> @endif
                                                    <input type="text" class="form-control text-center" id="ci" value="{{$inscripcion->alumno->numero_documento}}" readonly>
                                                </div>
                                                <div class="col-4 col-lg-1 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="asistencia-{{$key}}">Asistencia</label> @endif
                                                    <div class="text-center">
                                                        <button type="button" class="btn @if (old('detalles.' . $key . '.estado') != 'PR') btn-outline-danger @else btn-success @endif ausente-presente-btn" data-id={{$key}}><span class="ausente-presente-btn-text-{{$key}}">@if (old('detalles.' . $key . '.estado') != 'PR') Ausente @else Presente @endif</span></button>
                                                        <input type="hidden" id="asistencia-{{$key}}" name="detalles[{{$key}}][estado]" @if (old('detalles.' . $key . '.estado') != 'PR') value="AU" @else value="PR" @endif>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-lg-1 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="puntaje_obtenido-{{$key}}">Puntaje</label> @endif
                                                    <input type="text" class="form-control text-center puntaje_obtenido @error('detalles.' . $key . '.puntaje_obtenido') is-invalid @enderror" id="puntaje_obtenido-{{$key}}" name="detalles[{{$key}}][puntaje_obtenido]" value="{{old('detalles.' . $key . '.puntaje_obtenido')}}" placeholder="0-100" @if (old('detalles.' . $key . '.estado') != 'PR') disabled @endif>
                                                    @error('detalles.' . $key . '.puntaje_obtenido')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
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
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('cursos_notas_ubs.show', $curso->id)}}">Cancelar</button>
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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.cursos.notas.scripts.create-scripts')
    @endsection
@endcan
