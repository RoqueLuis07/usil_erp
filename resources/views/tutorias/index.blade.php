@can('ver_tutorias')
    @extends('layouts.master')
    @section('title') Tutorías @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
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
            @slot('title') Tutorías @endslot
        @endcomponent

        @include('tutorias.scripts.messages-scripts')
        @include('tutorias.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Tutorías</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="tutorias-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_tutorias')
                                        <a type="button" class="btn btn-success" href="{{route('tutorias.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="tutorias-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            <th class="sort" data-sort="semestre">Semestre</th>
                                            <th class="sort" data-sort="modalidad">Modalidad</th>
                                            <th>Inicio - Fin</th>
                                            <th>Cant. Alumnos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($tutorias as $tutoria)
                                            <tr>
                                                <td>{{$tutoria->id}}</td>
                                                <td class="materia">{{$tutoria->materia->nombre_fantasia}}</td>
                                                <td class="docente">@if ($tutoria->docente_id) {{$tutoria->docente->primer_nombre}} {{$tutoria->docente->primer_apellido}} @endif</td>
                                                <td class="semestre">{{$tutoria->semestre->nombre}}</td>
                                                <td class="modalidad">{{$tutoria->modalidad->nombre}}</td>
                                                <td>@if ($tutoria->fecha_inicio && $tutoria->fecha_fin) {{\Carbon\Carbon::parse($tutoria->fecha_inicio)->format('d/m/Y')}} - {{\Carbon\Carbon::parse($tutoria->fecha_fin)->format('d/m/Y')}} @endif</td>
                                                <td>{{number_format($tutoria->alumnos->count(), 0, ',', '.')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($tutoria->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($tutoria->estado == 'EC')
                                                            bg-warning-subtle text-warning text-uppercase"> En Curso
                                                        @elseif ($tutoria->estado == 'FI')
                                                            bg-danger-subtle text-danger text-uppercase"> Finalizado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('tutorias.show', $tutoria->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_tutorias')
                                                        @if ($tutoria->estado == 'AC')
                                                            <a type="button" class="btn btn-sm btn-info" href="{{route('tutorias.edit', $tutoria->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                        @endif
                                                    @endcan
                                                    @can('eliminar_tutorias')
                                                        @if ($tutoria->estado == 'AC')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$tutoria->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                        @endif
                                                    @endcan
                                                    @if (Auth::user()->can('ver_clases_tutorias') || Auth::user()->can('ver_actas_tutorias') || Auth::user()->can('ver_evaluaciones_tutorias'))
                                                        <div class="btn-group dropdown" role="group">
                                                            <button id="dropdown-mas" type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-add-line align-bottom mb-0"></i> Más
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-mas" aria-labelledby="dropdown-mas">
                                                                @can('ver_clases_tutorias')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('tutorias_clases.index', $tutoria->id)}}">Clases</a></li>
                                                                @endcan
                                                                @can('ver_actas_tutorias')
                                                                    @if ($tutoria->alumnos->where('cantidad_asistencias', $tutoria->cantidad_clases)->where('estado', 'EC')->count() > 0 || ($tutoria->acta && $tutoria->alumnos->where('estado', 'EC')->count() == 0) && $tutoria->estado != 'FI')
                                                                        <li><a class="dropdown-item dropdown-item-mas" href="{{route('tutorias_evaluaciones.show_acta', $tutoria->id)}}">Acta Evaluación</a></li>
                                                                    @endif
                                                                @endcan
                                                                @can('ver_evaluaciones_tutorias')
                                                                    @if ($tutoria->acta && $tutoria->alumnos->where('estado', 'EC')->count() > 0)
                                                                        <li><a class="dropdown-item dropdown-item-mas" href="{{route('tutorias_evaluaciones.show', $tutoria->id)}}">Evaluación</a></li>
                                                                    @endif
                                                                @endcan
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningúna tutoría según tus parámetros de búsqueda.</p>
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
        @include('tutorias.scripts.index-scripts')
    @endsection
@endcan
