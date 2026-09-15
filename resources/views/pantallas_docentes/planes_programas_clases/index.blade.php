@if (Auth::user()->can('ver_planes_clases_docentes_pantalla') || Auth::user()->can('ver_programas_clases_docentes_pantalla'))
    @extends('layouts.master')
    @section('title') Planes de Clases y Programas de Estudio @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            .dropdown-menu-plantillas {
                background-color: #3C80E6!important;
            }
            .dropdown-item-plantillas {
                color: white!important;
            }
            .dropdown-item-plantillas:hover {
                background-color: #9EC4FE!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.planes_programas_clases.scripts.messages-scripts')
        @include('pantallas_docentes.planes_programas_clases.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Planes de Clases y Programas de Estudio</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="planes_clases-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-3">
                                    <div class="btn-group dropdown" role="group">
                                        <button id="dropdown-plantillas" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Descargar Plantillas de Ejemplo">
                                            <i class="ri-download-line align-bottom mb-0 me-2"></i> Plantillas
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-plantillas" aria-labelledby="dropdown-plantillas">
                                            <li><a class="dropdown-item dropdown-item-plantillas" href="{{ asset('storage/planes_clases/planificacion_clase_2025_2_usil.doc') }}" download="planificacion_clase_2025_2_usil.doc">Plan de Clases</a></li>
                                            {{-- <li><a class="dropdown-item dropdown-item-plantillas" href="#" download="programa-estudio.xlsx">Programa de Estudio</a></li> --}}
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap text-center" id="planes_clases-list">
                                    <thead class="table-light">
                                        <tr>
											<th>N°</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            @can('ver_planes_clases_docentes_pantalla')
                                                <th>Plan de Clase</th>
                                            @endcan
                                            @can('ver_programas_clases_docentes_pantalla')
                                                <th>Programa de Estudio</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse ($materias as $key => $materia)
                                            <tr>
                                                <td>{{$key + 1}}</td>
                                                <td class="materia">{{$materia->materia->nombre_fantasia}}</td>
                                                @can('ver_planes_clases_docentes_pantalla')
                                                    <td>
                                                        @if ($materia->url_plan_clase)
                                                            <a href="{{ asset($materia->url_plan_clase) }}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver plan de clases">
                                                                <img src="{{ asset('storage/pdf.png') }}" alt="pdf-icon" style="width: 50px; height: 50px;">
                                                            </a>
                                                            <div class="mt-2">
                                                                <button type="button" class="btn btn-sm btn-danger delete-adjunto-btn" data-id="{{$materia->id}}" data-tipo="Plan">Eliminar</button>
                                                                <form action="{{route('planes_programas_clases_docentes.destroy_planes_clases', $materia->id)}}" method="delete" id="delete-plan-clase-form-{{$materia->id}}">
                                                                    @csrf
                                                                </form>
                                                            </div>
                                                        @else
                                                            @can('adjuntar_planes_clases_docentes_pantalla')
                                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#subirPlanClaseModal-{{$materia->id}}">Adjuntar</button>
                                                            @endcan
                                                        @endif
                                                    </td>
                                                @endcan
                                                @can('ver_programas_clases_docentes_pantalla')
                                                    <td>
                                                        @if ($materia->url_programa_clase)
                                                            <a href="{{ asset($materia->url_programa_clase) }}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver programa de clases">
                                                                <img src="{{ asset('storage/pdf.png') }}" alt="pdf-icon" style="width: 50px; height: 50px;">
                                                            </a>
                                                            <div class="mt-2">
                                                                <button type="button" class="btn btn-sm btn-danger mt-2 delete-adjunto-btn" data-id="{{$materia->id}}" data-tipo="Programa">Eliminar</button>
                                                                <form action="{{route('planes_programas_clases_docentes.destroy_programas_clases', $materia->id)}}" method="delete" id="delete-programa-clase-form-{{$materia->id}}">
                                                                    @csrf
                                                                </form>
                                                            </div>
                                                        @else
                                                            @can('adjuntar_programas_clases_docentes_pantalla')
                                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#subirProgramaClaseModal-{{$materia->id}}">Adjuntar</button>
                                                            @endcan
                                                        @endif
                                                    </td>
                                                @endcan
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="4">No tienes materias asignadas actualmente.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún plan de clase según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<div class="col-lg-6">
									<span class="text-muted" id="mostrando"></span>
										<br>
									<span class="text-muted">Total: {{$materias->count()}}</span>
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
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.planes_programas_clases.scripts.index-scripts')
    @endsection
@endif
