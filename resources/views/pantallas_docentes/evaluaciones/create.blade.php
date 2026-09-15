@can('crear_evaluaciones_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Agregar Evaluación @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('materias_evaluaciones.store')}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="SI">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva Evaluación</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$materia->nombre_fantasia}}" readonly>
                                    <input type="hidden" name="materia" value="{{$materia->id}}">
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$docente->primer_nombre}} {{$docente->primer_apellido}}" readonly>
                                    <input type="hidden" name="docente" value="{{$docente->id}}">
                                </div>
                                <div class="col-lg-6 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$semestre->nombre}}" readonly>
                                        <input type="hidden" name="semestre" value="{{$semestre->id}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="evaluacion">Evaluacion</label>
                                    <input type="text" class="form-control" value="{{$evaluacion->nombre}}" readonly>
                                    <input type="hidden" id="evaluacion_id" name="evaluacion" value="{{$evaluacion->id}}">
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" value="{{$carrera->nombre_fantasia}}" readonly>
                                    <input type="hidden" name="carrera" value="{{$carrera->id}}">
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" value="{{$carrera->programa->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Lista de alumnos</h5>
                                    <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                        @foreach ($alumnos as $key => $alumno)
                                            <input type="hidden" id="cantidad_filas" value="{{$alumnos->count()}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-1 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="nro_documento">Documento N°</label> @endif
                                                    <input type="text" class="form-control text-center" id="nro_documento" value="{{number_format($alumno->numero_documento, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-3 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="alumno-{{$key}}">Alumno</label> @endif
                                                    <input type="text" class="form-control" id="alumno-{{$key}}" value="{{$alumno->primer_apellido}}, {{$alumno->primer_nombre}}" readonly>
                                                    <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$alumno->id}}">
                                                </div>
                                                <div class="col-lg-1 mb-3 text-center @if ($evaluacion->tipo_evaluacion_id == 1) d-none @endif">
                                                    @if ($key == 0 && $evaluacion->tipo_evaluacion_id != 1) <label class="form-label" for="ausente-presente-input-{{$key}}">Asistencia</label> @endif
                                                    @if ($evaluacion->tipo_evaluacion_id != 1)
                                                        <button type="button" class="btn btn-outline-danger ausente-presente-btn" data-id="{{$key}}"><span class="ausente-presente-btn-text-{{$key}}">Ausente</span></button>
                                                    @endif
                                                    <input type="hidden" class="form-control text-center" id="ausente-presente-input-{{$key}}" name="detalles[{{$key}}][observacion]" value="">
                                                </div>
                                                <div class="col-lg-2 mb-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="puntaje_obtenido-{{$key}}">Puntaje Obtenido</label> @endif
                                                    <input type="text" class="form-control text-center puntaje_obtenido @error('detalles.' . $key . '.puntaje_obtenido') is-invalid @enderror" id="puntaje_obtenido-{{$key}}" name="detalles[{{$key}}][puntaje_obtenido]" placeholder="0 - @if ($evaluacion->id == 1 || $evaluacion->id == 2 || $evaluacion->id == 3) 30 @else 40 @endif" data-id="{{$key}}" readonly>
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
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('pantallas_docentes.index', Auth::id())}}">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Cargar</button>
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
        @include('pantallas_docentes.evaluaciones.scripts.create-scripts')
    @endsection
@endcan
