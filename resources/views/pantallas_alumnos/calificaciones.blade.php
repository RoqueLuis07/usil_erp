@can('ver_calificaciones_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Calificaciones Finales @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            .dropdown-menu-informes {
                background-color: #3C80E6!important;
            }
            .dropdown-item-informes {
                color: white!important;
            }
            .dropdown-item-informes:hover {
                background-color: #9EC4FE!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('index.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Calificaciones Finales</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-4 mb-3">
                            @can('ver_informes_academicos_alumnos_pantalla')
                                <div class="col-lg-12 text-end">
                                    <div class="btn-group dropdown" role="group">
                                        <button id="dropdown-informes" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Descargar Plantillas de Ejemplo">
                                            <i class="ri-download-line align-bottom mb-0 me-2"></i> Informe Académico
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-informes" aria-labelledby="dropdown-informes">
                                            <li><a class="dropdown-item dropdown-item-informes" href="{{ route('pantallas_alumnos.informes_academicos', ['id' => $alumno->id, 'tipo' => 'PY']) }}" target="_blank">Malla Nacional</a></li>
                                            @if ($alumno->programa_id == 1)
                                                <li><a class="dropdown-item dropdown-item-informes" href="{{ route('pantallas_alumnos.informes_academicos', ['id' => $alumno->id, 'tipo' => 'SIU']) }}" target="_blank">SIU</a></li>
                                                <li><a class="dropdown-item dropdown-item-informes" href="{{ route('pantallas_alumnos.informes_academicos', ['id' => $alumno->id, 'tipo' => 'ESPEJO']) }}" target="_blank">Espejo</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistNacional" role="tab" aria-selected="true">Grado Nacional</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistSiu" role="tab" aria-selected="true">SIU</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistEspejo" role="tab" aria-selected="true">Espejo</a>
                                    </li>
                                </ul>
                                {{-- Tab panes --}}
                                <div class="tab-content">
                                    <div class="tab-pane active show" id="tablistNacional" role="tabpanel">
                                        <div id="notas_nacionales-list">
                                            <div class="row g-4 mb-3">
                                                <div class="col-lg-3">
                                                    <div class="input-group">
                                                        <select class="selectpicker form-control" id="filtro_evaluacion_nacional">
                                                            <option value="" selected disabled>Filtrar por Tipo...</option>
                                                            <option value="ORDINARIO">ORDINARIO</option>
                                                            <option value="COMPLEMENTARIO">COMPLEMENTARIO</option>
                                                            <option value="EXTRAORDINARIO">EXTRAORDINARIO</option>
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
                                                    <table class="table align-middle table-nowrap" id="notas_nacionales-list">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th class="sort" data-sort="semestre_nacional">Semestre</th>
                                                                <th class="sort" data-sort="materia_nacional">Materia</th>
                                                                <th class="sort" data-sort="fecha_nacional">Fecha</th>
                                                                <th>Acta</th>
                                                                <th>Nota</th>
                                                                <th>Puntaje</th>
                                                                <th>Tipo</th>
                                                                <th>Periodo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list form-check-all text-center">
                                                            @forelse ($alumno_notas_nacional as $nota)
                                                                <tr>
                                                                    <td class="semestre_nacional">{{$nota->semestre_materia}}</td>
                                                                    <td class="materia_nacional">{{$nota->materia->nombre_fantasia}}</td>
                                                                    <td class="fecha_nacional">{{$nota->fecha_evaluacion}}</td>
                                                                    <td>{{$nota->numero_acta}}</td>
                                                                    <td>{{$nota->calificacion}}</td>
                                                                    <td>{{$nota->puntos_examen}}</td>
                                                                    <td class="tipo_nacional">{{$nota->evaluacion}}</td>
                                                                    <td class="periodo_nacional">{{$nota->semestre->nombre}}</td>
                                                                </tr>
                                                            @empty
                                                                <tr class="text-center">
                                                                    <td colspan="5">No tienes calificaciones registradas.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th class="table-light text-center" colspan="8">Promedio General: {{ $promedio_nacional }}</th>
                                                        </tr>
                                                    <tfoot>
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
                                        <div id="notas_siu-list">
                                            <div class="row g-4 mb-3">
                                                <div class="col-lg-3">
                                                    <div class="input-group">
                                                        <select class="selectpicker form-control" id="filtro_evaluacion_siu">
                                                            <option value="" selected disabled>Filtrar por Tipo...</option>
                                                            <option value="ORDINARIO">ORDINARIO</option>
                                                            <option value="COMPLEMENTARIO">COMPLEMENTARIO</option>
                                                            <option value="EXTRAORDINARIO">EXTRAORDINARIO</option>
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
                                                    <table class="table align-middle table-nowrap" id="notas_siu-list">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th class="sort" data-sort="semestre_siu">Semestre</th>
                                                                <th class="sort" data-sort="materia_siu">Materia</th>
                                                                <th class="sort" data-sort="fecha_siu">Fecha</th>
                                                                <th>Acta</th>
                                                                <th>Nota</th>
                                                                <th>Puntaje</th>
                                                                <th>Tipo</th>
                                                                <th>Periodo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list form-check-all text-center">
                                                            @forelse ($alumno_notas_siu as $nota)
                                                                <tr>
                                                                    <td class="semestre_siu">{{$nota->semestre_materia}}</td>
                                                                    <td class="materia_siu">{{$nota->materia->nombre_fantasia}}</td>
                                                                    <td class="fecha_siu">{{$nota->fecha_evaluacion}}</td>
                                                                    <td>{{$nota->numero_acta}}</td>
                                                                    <td>{{$nota->calificacion}}</td>
                                                                    <td>{{$nota->puntos_examen}}</td>
                                                                    <td class="tipo_siu">{{$nota->evaluacion}}</td>
                                                                    <td class="periodo_siu">{{$nota->semestre->nombre}}</td>
                                                                </tr>
                                                            @empty
                                                                <tr class="text-center">
                                                                    <td colspan="5">No tienes calificaciones registradas.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th class="table-light text-center" colspan="8">Promedio General: ---</th>
                                                        </tr>
                                                    <tfoot>
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
                                    <div class="tab-pane" id="tablistEspejo" role="tabpanel">
                                        <div id="notas_espejos-list">
                                            <div class="row g-4 mb-3">
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="search-box ms-2">
                                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                                            <i class="ri-search-line search-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="table-responsive table-card mt-3 mb-1">
                                                    <table class="table align-middle table-nowrap text-center" id="notas_espejos-list">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th colspan="3" style="border-right: 2px solid black">Malla Nacional ({{Str::title($alumno->carrera_paraguay)}})</th>
                                                                <th colspan="3" style="border-right: 2px solid black">SIU ({{Str::title($alumno->carrera_siu)}})</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr>
                                                                <th class="sort" data-sort="semestre_paraguay">Semestre</th>
                                                                <th class="sort" data-sort="materia_paraguay">Materia</th>
                                                                <th class="sort" data-sort="calificacion_paraguay" style="border-right: 2px solid black">Calificación</th>
                                                                <th class="sort" data-sort="semestre_usa">Semestre</th>
                                                                <th class="sort" data-sort="materia_usa">Materia</th>
                                                                <th class="sort" data-sort="calificacion_usa" style="border-right: 2px solid black">Calificación</th>
                                                                <th class="sort" data-sort="periodo_espejo">Periodo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list form-check-all">
                                                            @foreach ($espejos as $espejo)
                                                                <tr>
                                                                    <td class="semestre_paraguay">{{$espejo['semestre_materia_paraguay']}}</td>
                                                                    <td class="materia_paraguay">{{$espejo['materia_paraguay']}}</td>
                                                                    <td class="calificacion_paraguay" style="border-right: 2px solid black">{{$espejo['nota_paraguay']}}</td>
                                                                    @php
                                                                        $text_color = '';
                                                                        if ($espejo['estado']) {
                                                                            $text_color = 'text-danger';
                                                                        }
                                                                    @endphp
                                                                    <td class="semestre_usa {{$text_color}}">{{$espejo['semestre_materia_siu']}}</td>
                                                                    <td class="materia_usa {{$text_color}}">{{$espejo['materia_siu']}}</td>
                                                                    <td class="calificacion_usa {{$text_color}}" style="border-right: 2px solid black">{{$espejo['nota_siu']}}</td>
                                                                    <td class="periodo_espejo">{{$espejo['periodo']}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot class="table-light">
                                                            <tr>
                                                                <td colspan="3">Total de Materias: {{$cantidad_materias_paraguay}}</td>
                                                                <td colspan="3">Total de Materias: {{$cantidad_materias_siu}}</td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <div class="noresults" style="display: none">
                                                        <div class="text-center">
                                                            <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                            <h5 class="mt-2">Sin resultados.</h5>
                                                            <p class="text-muted mb-0">No pudimos encontrar ningún examen según tus parámetros de búsqueda.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="d-flex justify-content-end">
                                                <div class="pagination-wrap hstack gap-2">
                                                    <a class="page-item pagination-prev disabled"><</a>
                                                    <ul class="pagination listjs-pagination mb-0"></ul>
                                                    <a class="page-item pagination-next">></a>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                {{-- /Tab panes --}}
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
        @include('pantallas_alumnos.scripts.calificaciones-scripts')
    @endsection
@endcan
