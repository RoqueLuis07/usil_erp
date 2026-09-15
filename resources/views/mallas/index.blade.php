@can('ver_mallas')
    @extends('layouts.master')
    @section('title') Mallas @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Mallas @endslot
        @endcomponent

        @include('mallas.scripts.messages-scripts')
        @include('mallas.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Mallas</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="mallas-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_mallas')
                                        <a type="button" class="btn btn-success" href="{{route('mallas.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="mallas-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="id">ID</th>
                                            <th class="sort" data-sort="programa">Programa</th>
                                            <th class="sort" data-sort="carrera">Carrera</th>
                                            <th class="sort" data-sort="tipo_malla">Tipo de Malla</th>
                                            <th>Materias</th>
                                            <th>Carga Horaria</th>
                                            <th>Créditos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($mallas as $malla)
                                            <tr>
                                                <td class="id">{{$malla->id}}</td>
                                                <td class="programa">{{$malla->carrera->programa->nombre}}</td>
                                                <td class="carrera">{{$malla->carrera->nombre_fantasia}} - <small class="text-muted">{{$malla->carrera->nombre_real}}</small></td>
                                                <td class="tipo_malla">{{$malla->tipoMalla->nombre}}</td>
                                                <td>{{number_format($malla->mallaDetalles->count(), 0, ',', '.')}}</td>
                                                <td>{{number_format($malla->mallaDetalles->sum('carga_horaria'), 0, ',', '.')}}</td>
                                                <td>{{number_format($malla->mallaDetalles->sum('cantidad_creditos'), 0, ',', '.')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($malla->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($malla->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('mallas.show', $malla->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_mallas')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('mallas.edit', $malla->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($malla->estado == 'AC')
                                                        @can('inactivar_mallas')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$malla->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($malla->estado == 'IN')
                                                        @can('activar_mallas')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$malla->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_mallas')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$malla->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningúna malla según tus parámetros de búsqueda.</p>
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
        @include('mallas.scripts.index-scripts')
    @endsection
@endcan
