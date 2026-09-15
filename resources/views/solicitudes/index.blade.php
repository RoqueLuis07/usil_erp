@can('ver_solicitudes')
    @extends('layouts.master')
    @section('title') Solicitudes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Solicitudes @endslot
        @endcomponent

        @include('solicitudes.scripts.messages-scripts')
        @include('solicitudes.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Solicitudes</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="solicitudes-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_tipo" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Tipo...</option>
                                            <option value="AL">ALUMNO</option>
                                            <option value="AD">ACADEMICO</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-tipo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Tipo"><i class="ri-close-line"></i></button>
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
                                <table class="table align-middle table-nowrap" id="solicitudes-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha de Solicitud</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="tipo">Solicitud</th>
                                            <th class="sort" data-sort="programa">Programa</th>
                                            <th class="sort" data-sort="semestre">Semestre</th>
                                            <th class="sort" data-sort="tipo_generacion">Tipo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($solicitudes as $solicitud)
                                            <tr>
                                                <td class="id">{{$solicitud->id}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/y H:i:s')}}</td>
                                                <td class="alumno">{{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}</td>
                                                <td class="tipo">{{$solicitud->tipoSolicitud->nombre}}</td>
                                                <td class="programa">{{$solicitud->programa->nombre}}</td>
                                                <td class="semestre">{{$solicitud->semestre->nombre}}</td>
                                                <td class="tipo_generacion d-none">{{$solicitud->tipo_generacion}}</td>
                                                <td>
                                                    @if ($solicitud->tipo_generacion == 'AL')
                                                        ALUMNO
                                                    @elseif ($solicitud->tipo_generacion == 'AD')
                                                        ACADEMICO
                                                    @endif
                                                </td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($solicitud->estado == 'PE')
                                                            bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                        @elseif ($solicitud->estado == 'AP')
                                                            bg-primary-subtle text-primary text-uppercase"> Aprobado
                                                        @elseif ($solicitud->estado == 'PA')
                                                            bg-warning-subtle text-warning text-uppercase"> Pagado
                                                        @elseif ($solicitud->estado == 'GE')
                                                            bg-warning-subtle text-warning text-uppercase"> Generado
                                                        @elseif ($solicitud->estado == 'PR')
                                                            bg-info-subtle text-info text-uppercase"> Para Retiro
                                                        @elseif ($solicitud->estado == 'EN')
                                                            bg-success-subtle text-success text-uppercase"> Entregado
                                                        @elseif ($solicitud->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('solicitudes.show', $solicitud->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('eliminar_solicitudes')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$solicitud->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna solicitud según tus parámetros de búsqueda.</p>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('solicitudes.scripts.index-scripts')
    @endsection
@endcan
