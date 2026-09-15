@can('crear_evaluaciones_materias_semestres')
    @extends('layouts.master')
    @section('title') Agregar Evaluación @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Evaluaciones @endslot
            @slot('title') Agregar Evaluación  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('materias_evaluaciones.store')}}" method="post" id="store-form">
                @csrf
                <input type="hidden" name="generado_docente" value="NO">
                <div class="col-lg-12">
                    <div class="card">
                        <!-- end card header -->
                        <div class="card-body form-steps">
                            <div class="text-center pt-3 pb-4 mb-1">
                                <h5>Nueva Evaluación</h5>
                            </div>
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="col-lg-2 progress-nav mb-4" id="custom-progress-bar">
                                    <div class="progress" style="height: 1px">
                                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                        <li class="nav-item" role="presentation" id="pasouno-btn">
                                            <button class="nav-link rounded-pill active position-relative" data-progressbar="custom-progress-bar" id="pills-infoGeneral-tab" data-bs-toggle="pill" data-bs-target="#infoGeneral-tab" type="button" role="tab" aria-controls="infoGeneral-tab" aria-selected="true" data-position="0">
                                                1
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounden-pill bg-danger d-none" id="span-verificar">Verificar</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-listaAlumnos-tab" data-bs-toggle="pill" data-bs-target="#listaAlumnos-tab" type="button" role="tab" aria-controls="listaAlumnos-tab" aria-selected="false" data-position="1">2</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="infoGeneral-tab" role="tabpanel" aria-labelledby="pills-infoGeneral-tab">
                                    <div>
                                        <div class="mb-5">
                                            <div>
                                                <h5 class="mb-1">Información General</h5>
                                                <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 mb-3">
                                                <label class="form-label" for="materia">Materia</label>
                                                <input type="text" class="form-control" id="materia" value="{{$materia->nombre_fantasia}}" readonly>
                                                <input type="hidden" id="carrera_id" name="carrera" value="{{$carrera->id}}">
                                                <input type="hidden" id="materia_id" name="materia" value="{{$materia->id}}">
                                            </div>
                                            <div class="col-lg-4 mb-3">
                                                <label class="form-label" for="docente">Docente</label>
                                                <input type="text" class="form-control" id="docente" name="docente" @if($semestre_malla_materia->docente_id) value="{{$semestre_malla_materia->docente->primer_nombre}} {{$semestre_malla_materia->docente->primer_apellido}}" @endif readonly>
                                            </div>
                                            <div class="col-lg-4 mb-3 d-flex flex-wrap justify-content-end">
                                                <div class="col-lg-3 text-center">
                                                    <label class="form-label" for="semestre">Semestre</label>
                                                    <input type="text" class="form-control text-center" id="semestre" value="{{$semestre->nombre}}" readonly>
                                                    <input type="hidden" id="semestre_id" name="semestre" value="{{$semestre->id}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="evaluacion">Tipo de Evaluación <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control @error('evaluacion') is-invalid @enderror" id="evaluacion" name="evaluacion">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($evaluaciones as $evaluacion)
                                                        <option value="{{$evaluacion->id}}" @if (old('evaluacion') == strval($evaluacion->id)) selected @endif>{{$evaluacion->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('evaluacion')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-end gap-3 mt-4">
                                        <div>
                                            <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" id="puntuar-btn" data-nexttab="#pills-listaAlumnos-tab" disabled>
                                                <i class="ri-arrow-right-line label-icon align-middle"></i>
                                                Puntuar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="listaAlumnos-tab" role="tabpanel" aria-labelledby="pills-listaAlumnos-tab">
                                    <div>
                                        <div class="mb-5">
                                            <div>
                                                <h5 class="mb-1">Lista de Alumnos</h5>
                                                <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                                            </div>
                                        </div>
                                        <div id="lista_alumnos">

                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-end align-items-start gap-3 mt-4">
                                        <div>
                                            <button type="button" class="btn btn-light btn-label left me-2 previoustab previoustab" data-previoustab="#pills-infoGeneral-tab" id="pasouno-volver-btn">
                                                <i class="ri-arrow-left-line label-icon align-middle"></i>
                                                Volver
                                            </button>
                                            <button type="button" class="btn btn-success" id="save-btn" data-url="{{route('materias_evaluaciones.show', ['materia' => $materia->id, 'semestre' => $semestre->id, 'carrera' => $carrera->id])}}">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('materias_evaluaciones.show', ['materia' => $materia->id, 'semestre' => $semestre->id, 'carrera' => $carrera->id])}}">Cancelar</button>
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
        @include('materias_semestres.evaluaciones.scripts.create-scripts')
    @endsection
@endcan
