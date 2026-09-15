@can('crear_evaluaciones_tutorias_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Agregar Evaluación de Tutoría @endsection
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
            <form action="{{route('tutorias_evaluaciones.store', $tutoria->id)}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="SI">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0">Nueva evaluación de tutoría</h4>
                                </div>
                            </div>
                            <!-- end card header -->
                            <div class="card-body">
                                <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="materia">Materia</label>
                                        <input type="text" class="form-control" id="materia" value="{{$tutoria->materia->nombre_fantasia}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="docente">Docente</label>
                                        <input type="text" class="form-control" id="docente" value="{{$tutoria->docente->primer_nombre}} {{$tutoria->docente->primer_apellido}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3 d-flex flex-wrap justify-content-end">
                                        <div class="col-lg-3 text-center">
                                            <label class="form-label" for="semestre">Semestre</label>
                                            <input type="text" class="form-control text-center" id="semestre" value="{{$tutoria->semestre->nombre}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="evaluacion">Tipo de Evaluación</label>
                                        <input type="text" class="form-control @error('evaluacion') is-invalid @enderror" id="evaluacion" value="{{$evaluacion->nombre}}" readonly>
                                        <input type="hidden" name="evaluacion" value="{{$evaluacion->id}}">
                                        @error('evaluacion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0">Lista de Alumnos</h4>
                                </div>
                            </div>
                            <!-- end card header -->
                            <div class="card-body">
                                @foreach ($tutoria->alumnos as $key => $detalle)
                                    <div class="row d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-1 mb-3 text-center">
                                            @if ($key == 0) <label class="form-label" for="nro_documento">Documento N°</label> @endif
                                            <input type="text" class="form-control text-center" id="nro_documento" value="{{number_format($detalle->alumno->numero_documento, 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            @if ($key == 0) <label class="form-label" for="detalles[{{$key}}][alumno]">Alumno</label> @endif
                                            <input type="text" class="form-control @error ('detalles.' . $key . '.alumno') is-invalid @enderror" id="detalles[{{$key}}][alumno]" value="{{$detalle->alumno->primer_apellido}}, {{$detalle->alumno->primer_nombre}}" readonly>
                                            <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$detalle->alumno_id}}">
                                            @error('detalles.' . $key . '.alumno')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-1 mb-3 text-center">
                                            @if ($key == 0) <label class="form-label" for="ausente-presente-input-{{$key}}">Asistencia</label> @endif
                                            <div>
                                                <button type="button" class="btn @error('detalles.' . $key . '.puntaje_obtenido') btn-success @else btn-outline-danger ausente-presente-btn @enderror @error ('detalles.' . $key . '.observacion') is-invalid @enderror" data-id="{{$key}}"><span class="ausente-presente-btn-text-{{$key}}">@error('detalles.' . $key . '.puntaje_obtenido') Presente @else Ausente @enderror</span></button>
                                                <input type="hidden" class="form-control text-center" id="ausente-presente-input-{{$key}}" name="detalles[{{$key}}][observacion]" @error('detalles.' . $key . '.puntaje_obtenido') value="{{old('detalles.' . $key . '.observacion')}}" @else value="AUSENTE" @enderror>
                                                @error('detalles.' . $key . '.observacion')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            @if ($key == 0) <label class="form-label" for="puntaje_obtenido-{{$key}}">Puntaje Obtenido</label> @endif
                                            <input type="text" class="form-control text-center puntaje_obtenido @error ('detalles.' . $key . '.puntaje_obtenido') is-invalid @enderror" id="puntaje_obtenido-{{$key}}" name="detalles[{{$key}}][puntaje_obtenido]" value="{{old('detalles.' . $key . '.puntaje_obtenido')}}" placeholder="0 - 100" data-id="{{$key}}" @if (old('detalles.' . $key . '.observacion') == 'AUSENTE') readonly @endif>
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
                <div class="row">
                    <div class="col-lg-12 text-end mb-3">
                        <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                        <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
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
        @include('tutorias.evaluaciones.scripts.create-scripts')
    @endsection
@endcan
