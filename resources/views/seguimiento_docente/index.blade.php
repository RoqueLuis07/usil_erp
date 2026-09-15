@can('ver_materias_clases')
    @extends('layouts.master')
    @section('title') Clases @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Clases @endslot
        @endcomponent

        @include('seguimiento_docente.scripts.messages-scripts')
        @include('seguimiento_docente.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Seguimiento Docente</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="row g-4 mb-3 d-flex justify-content-end">
                            <div class="col-lg-2">
                                <form autocomplete="off" method="POST" action="{{ route('seguimiento_docente.index') }}" id="buscar_form">
                                @csrf
                                    <div class="search-box ms-2">
                                        <input type="hidden" id="filtro_mes_input" name="filtro_mes" value="{{$filtro_mes}}">
                                        <input type="hidden" id="filtro_periodo_input" name="filtro_periodo" value="{{$filtro_periodo}}">
                                        <input type="hidden" id="filtro_programa_input" name="filtro_programa" value="{{$filtro_programa}}">
                                        <button class="btn btn-primary py-0 d-none" type="submit"></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row g-4 mb-3">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="selectpicker form-control" id="filtro_mes" data-live-search="true">
                                                <option value="" selected disabled>Filtrar por Mes...</option>
                                                @foreach ($meses as $mes)
                                                    <option value="{{$mes}}" @if ($filtro_mes == strval($mes)) selected @endif>{{Str::upper(\Carbon\Carbon::now()->month($mes)->translatedFormat('F'))}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="selectpicker form-control" id="filtro_periodo" data-live-search="true">
                                                <option value="" selected disabled>Filtrar por Período...</option>
                                                @foreach ($semestres as $semestre)
                                                    <option value="{{$semestre->id}}" @if ($filtro_periodo == strval($semestre->id)) selected @endif>{{$semestre->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="selectpicker form-control" id="filtro_programa" data-live-search="true">
                                                <option value="" selected disabled>Filtrar por Programa...</option>
                                                @foreach ($programas as $programa)
                                                    <option value="{{$programa->id}}" @if ($filtro_programa == strval($programa->id)) selected @endif>{{$programa->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row d-flex justify-content-end">
                                    <div class="col-lg-4 text-end">
                                        <a type="button" class="btn btn-warning" href="{{route('seguimiento_docente.generate_pdf', ['filtro_mes' => $filtro_mes, 'filtro_periodo' => $filtro_periodo, 'filtro_programa' => $filtro_programa])}}" target="_blank"><i class="ri-download-line"></i> PDF</a>
                                        <a type="button" class="btn btn-warning" href="#" target="_blank"><i class="ri-download-line"></i> Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive table-card mt-3 mb-1">
                            @foreach ($dias_semana as $dia)
                                @foreach ($fechas as $key => $fecha)
                                    @if ($key == $dia->id)
                                        @php
                                            $colspan = count($fecha);
                                        @endphp
                                    @endif
                                @endforeach
                                <table class="table align-middle table-bordered text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="5" width="65%">{{$dia->nombre}}</th>
                                            <th colspan="{{$colspan}}" width="35%">{{Str::upper(\Carbon\Carbon::now()->month($filtro_mes)->translatedFormat('F'))}}</th>
                                        </tr>
                                        <tr>
                                            <th width="5%">Carrera</th>
                                            <th width="20%">Docente</th>
                                            <th width="20%">Curso</th>
                                            <th width="15%">Sesión</th>
                                            <th width="5%">Sala</th>
                                            @foreach ($fechas as $key => $fecha)
                                                @if ($key == $dia->id)
                                                    @foreach ($fecha as $index => $value)
                                                        <th width="5%">{{$value}}</th>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="form-check-all">
                                        @foreach ($semestre_materias as $semestre_materia)
                                            @if ($dia->id == $semestre_materia->dia_semana_id)
                                                <tr>
                                                    <td>{{$semestre_materia->carrera}}</td>
                                                    <td>{{$semestre_materia->docente}}</td>
                                                    <td>{{Str::title($semestre_materia->materia)}}</td>
                                                    <td>{{Str::title($dia->nombre)}} de {{\Carbon\Carbon::parse($semestre_materia->hora_inicio)->format('H:i')}} a {{\Carbon\Carbon::parse($semestre_materia->hora_fin)->format('H:i')}}hs.</td>
                                                    <td>{{trim($semestre_materia->aula)}}</td>
                                                    @foreach ($fechas as $key => $fecha)
                                                        @if ($key == $dia->id)
                                                            @foreach ($fecha as $value)
                                                                @php
                                                                    $clase_existente = $clases_materias->first(function ($clase) use ($value, $semestre_materia) {
                                                                        return $clase->materia_id == $semestre_materia->materia_id
                                                                            && \Carbon\Carbon::parse($clase->fecha_hora)->day == $value;
                                                                    });
                                                                @endphp

                                                                @if ($clase_existente)
                                                                    @if ($clase_existente->alumnoAsistencias->count() > 0)
                                                                        <td class="bg-success">
                                                                            <div class="btn-group" role="group">
                                                                                <button id="botones" type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown"></button>
                                                                                <div class="dropdown-menu" aria-labelledby="botones">
                                                                                    <a class="dropdown-item" href="{{ route('seguimiento_docente.show', $clase_existente->id) }}">Visualizar</a>
                                                                                    @can('eliminar_materias_clases')
                                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$clase_existente->id}}">Eliminar</a>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    @elseif ($clase_existente->alumnoAsistencias->count() == 0)
                                                                        <td class="bg-warning">
                                                                            <div class="btn-group" role="group">
                                                                                <button id="botones" type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown"></button>
                                                                                <div class="dropdown-menu" aria-labelledby="botones">
                                                                                    <a class="dropdown-item" href="{{ route('seguimiento_docente.show', $clase_existente->id) }}">Visualizar</a>
                                                                                    @can('crear_alumnos_asistencias_materias_semestres')
                                                                                        <a class="dropdown-item" href="{{ route('alumnos_asistencias.create', ['materia' => $clase_existente->materia_id, 'semestre' => $clase_existente->semestre_id, 'docente' => $clase_existente->docente_id]) }}">+ Asistencias</a>
                                                                                    @endcan
                                                                                    @can('eliminar_materias_clases')
                                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$clase_existente->id}}">Eliminar</a>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                @else
                                                                    <td class="bg-danger" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Sin clase"></td>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('seguimiento_docente.scripts.index-scripts')
    @endsection
@endcan
