@can('ver_carreras')
    @extends('layouts.master')
    @section('title') Carreras @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Carreras @endslot
        @endcomponent

        @include('carreras.scripts.messages-scripts')
        @include('carreras.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Carreras</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="carreras-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_carreras')
                                        <a type="button" class="btn btn-success" href="{{route('carreras.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="carreras-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="nombre_fantasia">Nombre Fantasía</th>
                                            <th class="sort" data-sort="nombre_real">Nombre Real</th>
                                            <th class="sort" data-sort="programa">Programa</th>
                                            <th>Tipo de Carrera</th>
                                            <th>Doble Grado</th>
                                            <th>Cant. Semestres</th>
                                            <th>Modalidad</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($carreras as $carrera)
                                            <tr>
                                                <td class="id">{{$carrera->id}}</td>
                                                <td class="nombre_fantasia">{{$carrera->nombre_fantasia}}</td>
                                                <td class="nombre_real">{{$carrera->nombre_real}}</td>
                                                <td class="programa">{{$carrera->programa->nombre}}</td>
                                                <td class="tipo_carrera">{{$carrera->tipoCarrera->nombre}}</td>
                                                <td class="doble_grado">
                                                    <div class="form-check form-check-success text-center">
                                                        <input type="checkbox" class="form-check-input" @if ($carrera->doble_grado == true) checked @endif disabled>
                                                    </div>
                                                </td>
                                                <td class="cantidad_semestres">{{$carrera->cantidad_semestres}}</td>
                                                <td class="modalidad">{{$carrera->modalidad->nombre}}</td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($carrera->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($carrera->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal-{{$carrera->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                    @can('editar_carreras')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('carreras.edit', $carrera->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($carrera->estado == 'AC')
                                                        @can('inactivar_carreras')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$carrera->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($carrera->estado == 'IN')
                                                        @can('activar_carreras')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$carrera->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_carreras')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$carrera->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningúna carrera según tus parámetros de búsqueda.</p>
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
        @include('carreras.scripts.index-scripts')
    @endsection
@endcan
