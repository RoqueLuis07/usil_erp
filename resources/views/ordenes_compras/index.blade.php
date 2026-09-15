@can('ver_compras_ordenes')
    @extends('layouts.master')
    @section('title') Ordenes de Compras @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Ordenes de Compras @endslot
        @endcomponent

        @include('ordenes_compras.scripts.messages-scripts')
        @include('ordenes_compras.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Ordenes de Compras</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="ordenes_compras-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_compras_ordenes')
                                        <a type="button" class="btn btn-success" href="{{route('ordenes_compras.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="ordenes_compras-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="id">ID</th>
                                            <th class="sort" data-sort="fecha_creacion">Fecha Creación</th>
                                            <th class="sort" data-sort="proveedor">Proveedor</th>
                                            <th>Monto Total</th>
                                            <th>Condición</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($ordenes_compras as $orden_compra)
                                            <tr>
                                                <td class="id">{{$orden_compra->id}}</td>
                                                <td class="fecha_creacion">{{Carbon\Carbon::parse($orden_compra->created_at)->format('d/m/Y H:i')}}</td>
                                                <td class="proveedor">{{$orden_compra->proveedor->razon_social}} - <small class="text-muted">{{$orden_compra->proveedor->ruc}}</small></td>
                                                @php
                                                    if ($orden_compra->moneda_id == 1) {
                                                        $decimales = 0;
                                                    } else {
                                                        $decimales = 2;
                                                    }
                                                @endphp
                                                <td>{{number_format($orden_compra->monto_total, $decimales, ',', '.')}} {{ $orden_compra->moneda->codigo }}</td>
                                                <td>
                                                    @if ($orden_compra->condicion_compra == 'CO')
                                                        CONTADO
                                                    @elseif ($orden_compra->condicion_compra == 'CR')
                                                        CREDITO ({{$orden_compra->credito_a}} días)
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($orden_compra->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($orden_compra->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Aprobada
                                                        @elseif ($orden_compra->estado == 'CO')
                                                            bg-info-subtle text-info text-uppercase"> Completada
                                                        @elseif ($orden_compra->estado == 'AN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulada
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('ordenes_compras.show', $orden_compra->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($orden_compra->estado == 'PE')
                                                        @can('editar_compras_ordenes')
                                                            <a type="button" class="btn btn-sm btn-info" href="{{route('ordenes_compras.edit', $orden_compra->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                        @endcan
                                                        @can('reimprimir_compras_ordenes')
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('ordenes_compras.imprimir', $orden_compra->id)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Imprimir"><i class="ri-printer-fill"></i></a>
                                                        @endcan
                                                        @can('aprobar_compras_ordenes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{$orden_compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                        @can('inactivar_compras_ordenes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$orden_compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($orden_compra->estado == 'AP')
                                                        @can('reimprimir_compras_ordenes')
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('ordenes_compras.imprimir', $orden_compra->id)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Imprimir"><i class="ri-printer-fill"></i></a>
                                                        @endcan
                                                        @can('cargar_facturas_compras_ordenes')
                                                            <a class="btn btn-sm btn-success" href="{{ route('compras.create_from_orden', $orden_compra->id) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Cargar Factura"><i class="ri-file-list-3-line"></i></a>
                                                        @endcan
                                                        @can('desaprobar_compras_ordenes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveModal-{{$orden_compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Desaprobar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($orden_compra->estado == 'AN')
                                                        @can('activar_compras_ordenes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$orden_compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Desanular"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                        @can('eliminar_compras_ordenes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$orden_compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                        @endcan
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna órden de compra según tus parámetros de búsqueda.</p>
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
        @include('ordenes_compras.scripts.index-scripts')
    @endsection
@endcan
