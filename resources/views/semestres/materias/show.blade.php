@can('ver_materias_periodos')
    @extends('layouts.master')
    @section('title') Ver Materias de la Carrera @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Semestre {{$semestre_malla->semestre->nombre}} @endslot
            @slot('title') Ver Materias de la Carrera @endslot
        @endcomponent

        @include('semestres.materias.modals.show-modals')
        @include('semestres.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-0">Visualizar materias de la carrera</h4>
                        </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="malla">Carrera</label>
                                <input type="text" class="form-control" id="malla" value=" {{$semestre_malla->malla->carrera->nombre_fantasia}} - {{$semestre_malla->malla->tipoMalla->nombre}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3 text-center d-flex flex-wrap justify-content-end">
                                <div class="col-lg-3 me-3">
                                    <label class="form-label" for="cantidad_materias">Cant. de Materias</label>
                                    <input class="form-control text-center" type="text" id="cantidad_materias" value="{{number_format($semestre_malla->semestreMallaMaterias->count(), 0, ',', '.')}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Carreras Habilitadas del Semestre</h4>
                            </div>
                            <div class="card-body">
                                @foreach ($semestre_malla->semestreMallaMaterias as $key => $detalle)
                                    <div class="mb-2">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-4 col-sm-12 mb-2 text-center">
                                                @if ($key == 0) <label class="form-label" for="materia">Materia</label> @endif
                                                <input type="text" class="form-control materia-{{$detalle->id}}" id="materia" value="{{$detalle->materia->nombre_fantasia}} - {{$detalle->materia->nombre_real}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$detalle->materia->nombre_real}}" data-title="{{$detalle->materia->nombre_real}}" readonly>
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                @if ($key == 0) <label class="form-label" for="docente-{{$detalle->id}}">Docente</label> @endif
                                                <input type="text" class="form-control" id="docente-{{$detalle->id}}" @if ($detalle->docente_id) value="{{$detalle->docente->primer_nombre}} {{$detalle->docente->primer_apellido}}" @endif readonly>
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                @if ($key == 0) <label class="form-label" for="aula-{{$detalle->id}}">Aula</label> @endif
                                                <input type="text" class="form-control text-center" id="aula-{{$detalle->id}}" value="{{$detalle->aula}}" readonly>
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                @if ($key == 0) <label class="form-label" for="observaciones-{{$detalle->id}}">Observaciones</label> @endif
                                                <textarea class="form-control" id="observaciones-{{$detalle->id}}" cols="30" rows="1" readonly>{{$detalle->observaciones}}</textarea>
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                @if ($key == 0) <label class="form-label" for="abierto-{{$detalle->id}}">Abierto ?</label> @endif
                                                <div class="form-check form-check-success d-flex align-items-center justify-content-center">
                                                    <input type="checkbox" class="form-check-input form-check-input-lg abierto" id="abierto-{{$detalle->id}}" @if ($detalle->estado == 'AC') checked @endif data-id="{{$detalle->id}}" style="width:2em; height:2em" @if(!Auth::user()->can('editar_estado_materias_periodos') || $detalle->semestreMalla->semestre->estado == 'IN') disabled @endif>
                                                </div>
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                @if ($key == 0) <div><label class="form-label" for="doble_grado">Acciones</label></div> @endif
                                                @can('editar_horarios_materias_periodos')
                                                    <a type="button" class="btn btn-sm @if ($detalle->horario == 'OK') btn-primary @else btn-danger @endif horario" href="{{route('semestres_mallas_materias_horarios.edit', $detalle->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Horario de la Materia" data-id="{{$detalle->id}}"><i class="ri-time-fill"></i></a>
                                                @endcan
                                                @can('editar_parametros_materias_periodos')
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editSemestreMallaMateriaModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="@if ($detalle->semestreMalla->semestre->estado == 'IN') Ver @else Editar @endif Detalles de la Materia" data-id="{{$detalle->id}}">
                                                        @if ($detalle->semestreMalla->semestre->estado == 'IN')
                                                            <i class="ri-eye-fill"></i>
                                                        @else
                                                            <i class="ri-edit-fill"></i>
                                                        @endif
                                                    </button>
                                                @endcan
                                                @if ($detalle->url_programa_clases)
                                                    @can('ver_programa_clases_materias_periodos')
                                                        <a type="button" class="btn btn-sm btn-success programa_clase" href="{{asset($detalle->url_programa_clases)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Programa de Estudio" data-id="{{$detalle->id}}"><i class="ri-newspaper-fill"></i></a>
                                                    @endcan
                                                @endif
                                                @if ($detalle->url_plan_clases)
                                                    @can('ver_plan_clases_materias_periodos')
                                                        <a type="button" class="btn btn-sm btn-warning plan_clase" href="{{asset($detalle->url_plan_clases)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Plan de Clases" data-id="{{$detalle->id}}"><i class="ri-article-fill"></i></a>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-end mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('semestres.show', $semestre_malla->semestre_id)}}">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('semestres.materias.scripts.show-scripts')
    @endsection
@endcan
