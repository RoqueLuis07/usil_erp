@can('ver_notas_cursos_ubs')
    @extends('layouts.master')
    @section('title') Ver Notas del Curso @endsection
    @section('css')
            <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Cursos @endslot
            @slot('title') Ver Notas del Curso  @endslot
        @endcomponent

        @include('ubs.cursos.scripts.messages-scripts')
        @include('ubs.cursos.notas.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar notas del curso</h4>
                            </div>
                            @can('crear_notas_cursos_ubs')
                                <div class="col-lg-6 text-end">
                                    @if ($curso->notas->count() == 0)
                                        <a class="btn btn-warning" href="{{route('cursos_notas_ubs.create', $curso->id)}}">Cargar Puntaje</a>
                                    @endif
                                </div>
                            @endcan
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="curso">Curso</label>
                                                <input type="text" class="form-control" id="curso" value="{{$curso->nombre_fantasia}}" readonly>
                                            </div>
                                            <div class="col-lg-1 mb-3">
                                                <label class="form-label" for="llamado">N° de Llamado</label>
                                                <input type="text" class="form-control text-center" id="llamado" value="{{$curso->llamado}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Notas</h4>
                                </div>
                                <div class="card-body">
                                    @forelse ($curso->notas as $key => $nota)
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-3 text-center">
                                                <label class="form-label" for="alumno">Alumno</label>
                                                <input type="text" class="form-control text-center" id="alumno" value="{{$nota->alumno->primer_nombre}} {{$nota->alumno->primer_apellido}}" readonly>
                                            </div>
                                            <div class="col-lg-2 text-center">
                                                <label class="form-label" for="alumno_documento">Documento N°</label>
                                                <input type="text" class="form-control text-center" id="alumno_documento" value="{{$nota->alumno->numero_documento}}" readonly>
                                            </div>
                                            <div class="col-lg-1 text-center">
                                                <label class="form-label" for="puntaje_obtenido">Puntos</label>
                                                <input type="text" class="form-control text-center" id="puntaje_obtenido" value="{{$nota->puntaje_obtenido}}" readonly>
                                            </div>
                                            <div class="col-lg-1 text-center">
                                                <label class="form-label" for="calificacion">Calificación</label>
                                                <input type="text" class="form-control text-center" id="calificacion" value="{{$nota->calificacion}}" readonly>
                                            </div>
                                            @can('editar_notas_cursos_ubs')
                                                @if ($curso->estado == 'AC')
                                                    <div class="col-lg-1 text-center">
                                                        @if ($key == 0) <label class="form-label" for="accion">Acciones</label> @endif
                                                        <div class="mt-1" id="accion">
                                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editPuntajeModal-{{$nota->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></button>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endcan
                                        </div>
                                    @empty
                                        @foreach ($curso->inscripciones as $key => $inscripcion)
                                            <div class="row flex-wrap justify-content-center">
                                                <div class="col-lg-3 text-center">
                                                    @if ($key == 0) <label class="form-label" for="alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control text-center" id="alumno" value="{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}" readonly>
                                                </div>
                                                <div class="col-lg-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="alumno_documento">Documento N°</label> @endif
                                                    <input type="text" class="form-control text-center" id="alumno_documento" value="{{$inscripcion->alumno->numero_documento}}" readonly>
                                                </div>
                                                <div class="col-lg-1 text-center">
                                                    @if ($key == 0) <label class="form-label" for="puntaje_obtenido">Puntos</label> @endif
                                                    <input type="text" class="form-control text-center" id="puntaje_obtenido" value="N/A" readonly>
                                                </div>
                                                <div class="col-lg-1 text-center">
                                                    @if ($key == 0) <label class="form-label" for="calificacion">Calificación</label> @endif
                                                    <input type="text" class="form-control text-center" id="calificacion" value="N/A" readonly>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('cursos.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.cursos.notas.scripts.show-scripts')
    @endsection
@endcan
