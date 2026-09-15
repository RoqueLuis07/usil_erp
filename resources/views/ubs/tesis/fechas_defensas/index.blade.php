@can('ver_fechas_defensas_tesis_ubs')
    @extends('layouts.master')
    @section('title') Fechas de Defensa @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Tesis @endslot
            @slot('title') Fechas de Defensa @endslot
        @endcomponent

        @include('ubs.tesis.fechas_defensas.scripts.messages-scripts')
        @include('ubs.tesis.fechas_defensas.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Fechas de Defensa</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="fechas_defensas_tesis-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_fechas_defensas_tesis_ubs')
                                        <a class="btn btn-success" href="{{route('tesis_parametros_ubs.fechas_defensas_create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="fechas_defensas_tesis-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="hora">Hora</th>
                                            <th class="sort" data-sort="estado">Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($fechas as $fecha)
                                            <tr>
                                                <td>{{$fecha->id}}</td>
                                                <td class="fecha">{{\Carbon\Carbon::parse($fecha->fecha)->format('d/m/Y')}}</td>
                                                <td class="hora">{{\Carbon\Carbon::parse($fecha->hora)->format('H:i')}}</td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($fecha->estado == 'LI')
                                                            bg-success-subtle text-success text-uppercase"> Libre
                                                        @elseif ($fecha->estado == 'OC')
                                                            bg-danger-subtle text-danger text-uppercase"> Ocupado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('tesis_parametros_ubs.fechas_defensas_show', $fecha->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_fechas_defensas_tesis_ubs')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('tesis_parametros_ubs.fechas_defensas_edit', $fecha->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @can('eliminar_fechas_defensas_tesis_ubs')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$fecha->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna fecha de defensa según tus parámetros de búsqueda.</p>
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
        <div class="row">
            <div class="col-lg-12 text-end mb-3">
                <a type="button" class="btn btn-danger me-2" href="{{route('tesis_parametros_ubs.index')}}">Volver</a>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        @include('ubs.tesis.fechas_defensas.scripts.index-scripts')
    @endsection
@endcan
