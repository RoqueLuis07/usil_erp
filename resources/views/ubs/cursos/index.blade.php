@can('ver_cursos_ubs')
    @extends('layouts.master')
    @section('title') Cursos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <style>
            .dropdown-menu-mas {
                background-color: #3C80E6!important;
            }
            .dropdown-item-mas {
                color: white!important;
            }
            .dropdown-item-mas:hover {
                background-color: #9EC4FE!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Cursos @endslot
        @endcomponent

        @include('ubs.cursos.scripts.messages-scripts')
        @include('ubs.cursos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Cursos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="cursos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_cursos_ubs')
                                        <a type="button" class="btn btn-success" href="{{route('cursos.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="cursos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="nombre_fantasia">Nombre Fantasía</th>
                                            <th class="sort" data-sort="nombre_real">Nombre Real</th>
                                            <th class="sort" data-sort="tipo_curso">Tipo de Curso</th>
                                            <th class="sort" data-sort="fecha_apertura">Apertura</th>
                                            <th class="sort" data-sort="fecha_fin">Fin</th>
                                            <th>Cant. Horas</th>
                                            <th>Modalidad</th>
                                            <th>Cant. Módulos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($cursos as $curso)
                                            <tr>
                                                <td class="id">{{$curso->id}}</td>
                                                <td class="nombre_fantasia">{{$curso->nombre_fantasia}}</td>
                                                <td class="nombre_real">{{$curso->nombre_real}}</td>
                                                <td class="tipo_curso">{{$curso->tipoCurso->nombre}}</td>
                                                <td class="fecha_apertura">{{Carbon\Carbon::parse($curso->fecha_apertura)->format('d/m/Y')}}</td>
                                                <td class="fecha_fin">{{Carbon\Carbon::parse($curso->fecha_fin)->format('d/m/Y')}}</td>
                                                <td>{{$curso->cantidad_horas}}</td>
                                                <td>{{$curso->modalidad->nombre}}</td>
                                                <td>{{$curso->modulos->count()}}</td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($curso->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($curso->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-primary" href="{{route('cursos.show', $curso->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_cursos_ubs')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('cursos.edit', $curso->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($curso->estado == 'AC')
                                                        @can('inactivar_cursos_ubs')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$curso->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($curso->estado == 'IN')
                                                        @can('activar_cursos_ubs')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$curso->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_cursos_ubs')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$curso->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
                                                    @if (Auth::user()->can('editar_modulos_cursos_ubs') || Auth::user()->can('ver_asistencias_cursos_ubs') || Auth::user()->can('ver_notas_cursos_ubs') || Auth::user()->can('ver_certificados_cursos_ubs'))
                                                        <div class="btn-group dropdown" role="group">
                                                            <button id="dropdown-mas" type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-add-line align-bottom mb-0"></i> Más
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-mas" aria-labelledby="dropdown-mas">
                                                                @can('editar_modulos_cursos_ubs')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('cursos.edit_modulos', $curso->id)}}">Módulos</a></li>
                                                                @endcan
                                                                @can('ver_asistencias_cursos_ubs')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('alumnos_asistencias_ubs.show', $curso->id)}}">Asistencias</a></li>
                                                                @endcan
                                                                @if ($curso->evaluacion)
                                                                    @can('ver_notas_cursos_ubs')
                                                                        <li><a class="dropdown-item dropdown-item-mas" href="{{route('cursos_notas_ubs.show', $curso->id)}}">Notas</a></li>
                                                                    @endcan
                                                                @endif
                                                                @if ($curso->certificadosGenerados->count() > 0)
                                                                    @can('ver_certificados_generados_cursos_ubs')
                                                                        <li><a class="dropdown-item dropdown-item-mas" href="{{route('cursos.certificados_generados', $curso->id)}}">Certificados</a></li>
                                                                    @endcan
                                                                @else
                                                                    @can('generar_certificados_cursos_ubs')
                                                                        <li><a class="dropdown-item dropdown-item-mas" href="{{route('cursos_certificados.show', $curso->id)}}">Certificados</a></li>
                                                                    @endcan
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún curso según tus parámetros de búsqueda.</p>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('ubs.cursos.scripts.index-scripts')
    @endsection
@endcan
