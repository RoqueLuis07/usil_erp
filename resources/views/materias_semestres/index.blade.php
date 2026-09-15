@can('ver_materias_semestres')
    @extends('layouts.master')
    @section('title') Materias por Semestres @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            .dropdown-menu-clases {
                background-color: #29B768!important;
            }
            .dropdown-item-clases {
                color: white!important;
            }
            .dropdown-item-clases:hover {
                background-color: #93E3B8!important;
                color: black!important;
            }
            .dropdown-menu-asistencias {
                background-color: #29B768!important;
            }
            .dropdown-item-asistencias {
                color: white!important;
            }
            .dropdown-item-asistencias:hover {
                background-color: #93E3B8!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Materias por Semestres @endslot
        @endcomponent

        @include('materias_semestres.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Materias por Semestres</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="materias_semestres-list">
                            <div class="row g-4 mb-3 d-flex justify-content-end">
                                <div class="col-lg-2">
                                    <div class="search-box ms-2">
                                        <input type="text" class="form-control search" placeholder="Buscar...">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_semestre" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Semestre...</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-semestre-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Semestre"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_periodo" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Período...</option>
                                            @foreach ($semestres as $semestre)
                                                <option value="{{$semestre->nombre}}">{{$semestre->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-periodo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_docente" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Docente...</option>
                                            @foreach ($docentes as $docente)
                                                <option value="{{$docente->primer_nombre}} {{$docente->primer_apellido}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-docente-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_carrera" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Carrera...</option>
                                            @foreach ($carreras as $carrera)
                                                <option value="{{$carrera->nombre_fantasia}}" data-subtext="{{ $carrera->programa->nombre }}">{{$carrera->nombre_fantasia}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-carrera-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Carrera"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_programa" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Programa...</option>
                                            @foreach ($programas as $programa)
                                                <option value="{{$programa->nombre}}">{{$programa->nombre}}</option>
                                            @endforeach
                                            <option value="GENERADO POR MATERIA">GENERADO POR MATERIA</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-programa-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Programa"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_inscriptos" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Inscriptos...</option>
                                            <option value="10">0 - 10</option>
                                            <option value="20">11 - 20</option>
                                            <option value="30">21 - 30</option>
                                            <option value="40">31 - 40</option>
                                            <option value="50">41 - 50</option>
                                            <option value="51">+ 51</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-inscriptos-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Inscriptos"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="materias_semestres-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort text-center" data-sort="semestre">Semestre</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            <th class="sort" data-sort="carrera">Carrera</th>
                                            <th class="sort" data-sort="programa">Programa</th>
                                            <th class="sort text-center" data-sort="inscriptos">Cant. Alumnos</th>
                                            <th class="sort" data-sort="periodo">Período</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach($datos as $dato)
                                            <tr>
                                                <td class="semestre text-center">{{$dato['semestre']}}</td>
                                                <td class="materia">{{$dato['materia']}}</td>
                                                <td class="docente">{{$dato['docente']}}</td>
                                                <td class="carrera">{{$dato['carrera']}}</td>
                                                <td class="programa">{{$dato['programa']}}</td>
                                                <td class="inscriptos text-center">{{$dato['inscriptos']}}</td>
                                                <td class="periodo">{{$dato['periodo']}}</td>
                                                <td>
                                                    @can('ver_evaluaciones_materias_semestres')
                                                        <a type="button" class="btn btn-sm btn-warning" href="{{route('materias_evaluaciones.show', ['materia' => $dato['materia_id'], 'semestre' => $dato['semestre_id'], 'carrera' => $dato['carrera_id']])}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Evaluaciones"><i class="ri-article-fill"></i></a>
                                                    @endcan
                                                    @can('crear_clases_materias_semestres')
                                                        <div class="btn-group" role="group">
                                                            <button id="dropdown-clases" type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-add-line align-bottom mb-0"></i> Clase
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-clases" aria-labelledby="dropdown-clases">
                                                                <li><a class="dropdown-item dropdown-item-clases" href="{{route('clases_materias.create_materia', ['materia' => $dato['materia_id'], 'semestre' => $dato['semestre_id'], 'carrera' => $dato['carrera_id']])}}">Materia</a></li>
                                                                <li><a class="dropdown-item dropdown-item-clases" href="{{route('clases_materias.create_carrera', ['materia' => $dato['materia_id'], 'semestre' => $dato['semestre_id'], 'carrera' => $dato['carrera_id']])}}">Carrera</a></li>
                                                            </ul>
                                                        </div>
                                                    @endcan
                                                    @can('crear_alumnos_asistencias_materias_semestres')
                                                        <div class="btn-group" role="group">
                                                            <button id="dropdown-asistencias" type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-add-line align-bottom mb-0"></i> Asistencia
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-asistencias" aria-labelledby="dropdown-asistencias">
                                                                <li><a class="dropdown-item dropdown-item-asistencias" href="{{route('alumnos_asistencias.create_materia', ['materia' => $dato['materia_id'], 'semestre' => $dato['semestre_id'], 'carrera' => $dato['carrera_id']])}}">Materia</a></li>
                                                                <li><a class="dropdown-item dropdown-item-asistencias" href="{{route('alumnos_asistencias.create_carrera', ['materia' => $dato['materia_id'], 'semestre' => $dato['semestre_id'], 'carrera' => $dato['carrera_id']])}}">Carrera</a></li>
                                                            </ul>
                                                        </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningúna materia según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<div class="col-lg-6">
									<p class="small text-muted" id="mostrando"></p>
								</div>
								<div class="col-lg-6 d-flex justify-content-end">
									<div class="pagination-wrap hstack gap-2">
										<a class="page-item pagination-prev disabled"><</a>
										<ul class="pagination listjs-pagination mb-0"></ul>
										<a class="page-item pagination-next">></a>
									</div>
								</div>
							</div>
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
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('materias_semestres.scripts.index-scripts')
    @endsection
@endcan
