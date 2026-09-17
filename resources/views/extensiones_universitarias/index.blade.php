@can('ver_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Extensiones Universitarias @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
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
            @slot('li_1') Inicio @endslot
            @slot('title') Extensiones Universitarias @endslot
        @endcomponent

        @include('extensiones_universitarias.scripts.messages-scripts')
        @include('extensiones_universitarias.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-0">Lista de Extensiones Universitarias</h4>
                        </div>
                        <div class="col-lg-6 d-flex">
                            <div class="col-lg-9 text-end">
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#showReporteModal">Visualizar Reporte</button>
                                @can('generar_reportes_extensiones_universitarias_carrera_semestre')
                                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#showReporteCarreraSemestreModal">Reporte por Carrera y Semestre</button>
                                @endcan
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-end">
                                    <div class="btn-group dropdown" role="group">
                                        <button id="dropdown-plantillas" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Descargar Plantillas de Ejemplo">
                                            <i class="ri-download-line align-bottom mb-0 me-2"></i> Plantillas
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-plantillas" aria-labelledby="dropdown-plantillas">
                                            <li><a class="dropdown-item dropdown-item-plantillas" href="{{asset('storage/extensiones_universitarias/plantillas/propuesta-proyecto.xlsx')}}" download="propuesta-proyecto.xlsx">Propuesta de Proyecto</a></li>
                                            <li><a class="dropdown-item dropdown-item-plantillas" href="{{asset('storage/extensiones_universitarias/plantillas/informe-final.xlsx')}}" download="informe-final.xlsx">Informe Final</a></li>
                                            <li><a class="dropdown-item dropdown-item-plantillas" href="{{asset('storage/extensiones_universitarias/plantillas/registro-asistencia.xlsx')}}" download="registro-asistencia.xlsx">Registro de Asistencia</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="extensiones-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_extensiones_universitarias')
                                        <a type="button" class="btn btn-success" href="{{route('extensiones_universitarias.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="extensiones-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="id">ID</th>
                                            <th class="sort" data-sort="nombre">Proyecto</th>
                                            <th class="sort" data-sort="tipo_extension">Tipo de Proyecto</th>
                                            <th class="sort" data-sort="docente">Responsable</th>
                                            <th>Tiempo</th>
                                            <th>Cant. Alumnos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($extensiones as $extension)
                                            <tr>
                                                <td class="id">{{$extension->id}}</td>
                                                <td class="nombre">{{$extension->nombre}}</td>
                                                <td class="tipo_extension">{{$extension->tipoExtension->nombre}}</td>
                                                <td class="docente">{{$extension->docente->primer_nombre}} {{$extension->docente->primer_apellido}}</td>
                                                <td>{{number_format($extension->cantidad_horas, 2, ',', '.')}} @if ($extension->maxima_cantidad_horas != 1) horas @else hora @endif</td>
                                                <td>
                                                    {{$extension->extensionUniversitariaDetalles->count()}}
                                                    @if ($extension->extensionUniversitariaDetalles->count() == 1)
                                                        alumno
                                                    @else
                                                        alumnos
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($extension->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($extension->estado == 'AP')
                                                            bg-info-subtle text-info text-uppercase"> Aprobado
                                                        @elseif ($extension->estado == 'IN')
                                                            bg-info-subtle text-info text-uppercase"> Informado
                                                        @elseif ($extension->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @elseif ($extension->estado == 'CO')
                                                            bg-secondary-subtle text-secondary text-uppercase"> Completo
                                                        @elseif ($extension->estado == 'FI')
                                                            bg-success-subtle text-success text-uppercase"> Finalizado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('extensiones_universitarias.show', $extension->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($extension->estado == 'PE')
                                                        @can('editar_extensiones_universitarias')
                                                            <a type="button" class="btn btn-sm btn-info" href="{{route('extensiones_universitarias.edit', $extension->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                        @endcan
                                                        @can('aprobar_extensiones_universitarias')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                        @can('rechazar_extensiones_universitarias')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rechazar"><i class="bi bi-slash-circle"></i></button>
                                                        @endcan
                                                    @elseif ($extension->estado == 'AP' || $extension->estado == 'IN')
                                                        @can('anular_aprobacion_extensiones_universitarias')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Desaprobar"><i class="ri-arrow-left-line"></i></button>
                                                        @endcan
                                                    @elseif ($extension->estado == 'RE')
                                                        @can('anular_rechazo_extensiones_universitarias')
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#unrejectModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Rechazo"><i class="ri-arrow-left-line"></i></button>
                                                        @endcan
                                                    @elseif ($extension->estado == 'CO')
                                                        @can('finalizar_extensiones_universitarias')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#finishModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Finalizar"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                        @can('eliminar_extensiones_universitarias')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna extensión universitaria según tus parámetros de búsqueda.</p>
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
        @include('extensiones_universitarias.scripts.index-scripts')
    @endsection
@endcan
