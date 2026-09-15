@can('ver_compras')
    @extends('layouts.master')
    @section('title') Compras @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Compras @endslot
        @endcomponent

        @include('compras.scripts.messages-scripts')
        @include('compras.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Compras</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="compras-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_compras')
                                        <a type="button" class="btn btn-success" href="{{route('compras.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="compras-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="id">ID</th>
                                            <th class="sort" data-sort="fecha">Fecha Factura</th>
                                            <th class="sort" data-sort="proveedor">Proveedor</th>
                                            <th>N° OC</th>
                                            <th>Monto Total</th>
                                            <th>Condición</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($compras as $compra)
                                            <tr>
                                                <td class="id">{{$compra->id}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($compra->fecha)->format('d/m/Y')}}</td>
                                                <td class="proveedor">{{$compra->proveedor->razon_social}} - <small class="text-muted">{{$compra->proveedor->ruc}}</small></td>
                                                <td>
                                                    @if ($compra->orden_compra_id)
                                                        N° {{$compra->orden_compra_id}}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                @php
                                                    if ($compra->moneda_id == 1) {
                                                        $decimales = 0;
                                                    } else {
                                                        $decimales = 2;
                                                    }
                                                @endphp
                                                <td>{{number_format($compra->monto_total, $decimales, ',', '.')}} {{ $compra->moneda->codigo }}</td>
                                                <td>
                                                    @if ($compra->condicion_compra == 'CO')
                                                        CONTADO
                                                    @elseif ($compra->condicion_compra == 'CR')
                                                        CREDITO ({{$compra->credito_a}} días)
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($compra->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($compra->estado == 'CR')
                                                            bg-success-subtle text-success text-uppercase"> Crédito
                                                        @elseif ($compra->estado == 'OP')
                                                            bg-success-subtle text-success text-uppercase"> Orden de Pago
                                                        @elseif ($compra->estado == 'PA')
                                                            bg-success-subtle text-success text-uppercase"> Pagada
                                                        @elseif ($compra->estado == 'AN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulada
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('compras.show', $compra->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($compra->estado == 'PE' || $compra->estado == 'CR')
                                                        @can('crear_pagos_ordenes')
                                                            <a class="btn btn-sm btn-success" href="#" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Generar Orden de Pago"><i class="ri-file-list-3-fill"></i></a>
                                                        @endcan
                                                        @can('inactivar_compras')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($compra->estado == 'AN')
                                                        @can('eliminar_compras')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$compra->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna factura de compra según tus parámetros de búsqueda.</p>
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
        @include('compras.scripts.index-scripts')
    @endsection
@endcan
