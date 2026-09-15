@can('ver_puntajes_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Evaluación Continua @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('index.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Evaluación Continua</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistNacional" role="tab" aria-selected="true">Grado Nacional</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistSiu" role="tab" aria-selected="true">SIU</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tablistNacional" role="tabpanel">
                                <div id="puntajes_nacionales-list">
                                    <div class="row g-4 mb-3">
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <select class="selectpicker form-control" id="filtro_evaluacion_nacional">
                                                    <option value="" selected disabled>Filtrar por Tipo...</option>
                                                    @foreach ($evaluaciones as $evaluacion)
                                                        <option value="{{$evaluacion->nombre}}">{{$evaluacion->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-outline-danger" id="delete-evaluacion-nacional-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Tipo"><i class="ri-close-line"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <button class="btn btn-outline-success" id="add-periodo-nacional-filter-btn" data-id="{{$periodo_activo}}" @if ($periodo_activo == null) disabled @endif>Mostrar Periodo Activo</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="d-flex justify-content-end">
                                                <div class="search-box ms-2">
                                                    <input type="text" class="form-control search" placeholder="Buscar materia...">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap" id="puntajes_nacionales-list">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th class="sort" data-sort="materia_nacional">Materia</th>
                                                        <th>Puntaje</th>
                                                        <th class="sort" data-sort="tipo_nacional">Tipo</th>
                                                        <th class="sort" data-sort="fecha_nacional">Fecha</th>
                                                        <th class="sort" data-sort="periodo_nacional">Periodo</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all text-center">
                                                    @forelse ($puntajes_nacionales as $puntaje)
                                                        <tr>
                                                            <td class="materia_nacional">{{$puntaje->materia->nombre_fantasia}}</td>
                                                            <td>{{$puntaje->puntos_obtenidos}}</td>
                                                            <td class="tipo_nacional">{{$puntaje->evaluacion->nombre}}</td>
                                                            <td class="fecha_nacional">@if ($puntaje->creted_at) {{Carbon\Carbon::parse($puntaje->created_at)->format('d/m/Y')}} @else --- @endif</td>
                                                            <td class="periodo_nacional">{{$puntaje->semestre->nombre}}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">No tienes puntajes registrados.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div class="noresults" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún examen según tus parámetros de búsqueda.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="pagination-wrap hstack gap-2">
                                            <a class="page-item pagination-prev disabled"><</a>
                                            <ul class="pagination listjs-pagination mb-0"></ul>
                                            <a class="page-item pagination-next">></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane" id="tablistSiu" role="tabpanel">
                                <div id="puntajes_siu-list">
                                    <div class="row g-4 mb-3">
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <select class="selectpicker form-control" id="filtro_evaluacion_siu">
                                                    <option value="" selected disabled>Filtrar por Tipo...</option>
                                                    @foreach ($evaluaciones as $evaluacion)
                                                        <option value="{{$evaluacion->nombre}}">{{$evaluacion->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-outline-danger" id="delete-evaluacion-siu-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Tipo"><i class="ri-close-line"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <button class="btn btn-outline-success" id="add-periodo-siu-filter-btn" data-id="{{$periodo_activo}}" @if ($periodo_activo == null) disabled @endif>Mostrar Periodo Activo</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="d-flex justify-content-end">
                                                <div class="search-box ms-2">
                                                    <input type="text" class="form-control search" placeholder="Buscar materia...">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap" id="puntajes_siu-list">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th class="sort" data-sort="materia_siu">Materia</th>
                                                        <th>Puntaje</th>
                                                        <th class="sort" data-sort="tipo_siu">Tipo</th>
                                                        <th class="sort" data-sort="fecha_siu">Fecha</th>
                                                        <th class="sort" data-sort="periodo_siu">Periodo</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all text-center">
                                                    @forelse ($puntajes_siu as $puntaje)
                                                        <tr>
                                                            <td class="materia_siu">{{$puntaje->materia->nombre_fantasia}}</td>
                                                            <td>{{$puntaje->puntos_obtenidos}}</td>
                                                            <td class="tipo_siu">{{$puntaje->evaluacion->nombre}}</td>
                                                            <td class="fecha_siu">@if ($puntaje->creted_at) {{Carbon\Carbon::parse($puntaje->created_at)->format('d/m/Y')}} @else --- @endif</td>
                                                            <td class="periodo_siu">{{$puntaje->semestre->nombre}}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">No tienes puntajes registrados.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div class="noresults" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún examen según tus parámetros de búsqueda.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="pagination-wrap hstack gap-2">
                                            <a class="page-item pagination-prev disabled"><</a>
                                            <ul class="pagination listjs-pagination mb-0"></ul>
                                            <a class="page-item pagination-next">></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- end card -->
                <div class="row">
                    <div class="col-lg-12 text-center mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Volver</a>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection

    @section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_alumnos.scripts.puntajes-scripts')
    @endsection
@endcan
