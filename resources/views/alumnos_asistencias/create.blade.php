@can('crear_alumnos_asistencias_materias_semestres')
    @extends('layouts.master')
    @section('title') Agregar Asistencia @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Asistencias @endslot
            @slot('title') Agregar Asistencia  @endslot
        @endcomponent

        @include('alumnos_asistencias.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('alumnos_asistencias.store')}}" method="post" id="store-form">
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
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$materia->nombre_fantasia}}" readonly>
                                    <input type="hidden" name="materia" value="{{$materia->id}}">
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$docente->primer_nombre}} {{$docente->primer_apellido}}" readonly>
                                    <input type="hidden" name="docente" value="{{$docente->id}}">
                                </div>
                                @if (!$carrera->count() == 0)
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="carrera">Carrera</label>
                                        <input type="text" class="form-control" id="carrera" value="{{$carrera->nombre_fantasia}}" readonly>
                                        <input type="hidden" name="carrera" value="{{$carrera->id}}">
                                    </div>
                                @endif
                                <div class="@if (!$carrera->count() == 0) col-lg-3 @else col-lg-6 @endif mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="@if (!$carrera->count() == 0) col-lg-4 @else col-lg-2 @endif text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$semestre->nombre}}" readonly>
                                        <input type="hidden" name="semestre" value="{{$semestre->id}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="clase">Clase</label>
                                    <input type="text" class="form-control" value="{{$clase->tema_desarrollado}}" readonly>
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
                                                    <input type="text" class="form-control" id="alumno" value="{{$alumno->primer_apellido}}, {{$alumno->primer_nombre}}" readonly>
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
        @include('alumnos_asistencias.scripts.create-scripts')
    @endsection
@endcan
