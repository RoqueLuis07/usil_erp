@can('ver_pagos_ordenes')
    @extends('layouts.master')
    @section('title') Ordenes de Pagos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            .dropdown-menu-mas {
                background-color: #29B768!important;
            }
            .dropdown-item-mas {
                color: white!important;
            }
            .dropdown-item-mas:hover {
                background-color: #1C7D47!important;
                color: white!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Ordenes de Pagos @endslot
        @endcomponent

        @include('ordenes_pagos.scripts.messages-scripts')
        @include('ordenes_pagos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Ordenes de Pagos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="ordenes_pagos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_pagos_ordenes')
                                        <div class="btn-group dropdown" role="group">
                                            <button id="dropdown-mas" type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-add-line align-bottom mb-0"></i> Agregar
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-mas" aria-labelledby="dropdown-mas">
                                                <li><a class="dropdown-item dropdown-item-mas" href="{{route('ordenes_pagos.create', ['tipo' => 'PR'])}}">Pago a Proveedores</a></li>
                                                <li><a class="dropdown-item dropdown-item-mas" href="{{route('ordenes_pagos.create', ['tipo' => 'AN'])}}">Anticipo a Proveedores</a></li>
                                                <li><a class="dropdown-item dropdown-item-mas" href="{{route('ordenes_pagos.create', ['tipo' => 'GE'])}}">Pago de Gerencia</a></li>
                                            </ul>
                                        </div>
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
                                <table class="table align-middle table-nowrap" id="ordenes_pagos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="id">ID</th>
                                            <th class="sort" data-sort="fecha_creacion">Fecha Creación</th>
                                            <th class="sort" data-sort="compra">Factura N°</th>
                                            <th class="sort" data-sort="proveedor">Proveedor</th>
                                            <th class="sort" data-sort="tipo">Tipo</th>
                                            <th>Monto Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($ordenes_pagos as $orden_pago)
                                            <tr>
                                                <td class="id">{{$orden_pago->id}}</td>
                                                <td class="fecha_creacion">{{Carbon\Carbon::parse($orden_pago->created_at)->format('d/m/Y H:i')}}</td>
                                                <td class="compra">
                                                    @if ($orden_pago->compra)
                                                        {{ $orden_pago->compra->numero_factura }}
                                                    @else
                                                        ---
                                                    @endif
                                                </td>
                                                <td class="proveedor">{{$orden_pago->proveedor->razon_social}} - <small class="text-muted">{{$orden_pago->proveedor->ruc}}</small></td>
                                                <td class="tipo">
                                                    @if ($orden_pago->tipo == 'PR')
                                                        PROVEEDORES
                                                    @elseif ($orden_pago->tipo == 'GE')
                                                        GERENCIA
                                                    @elseif ($orden_pago->tipo == 'AN')
                                                        ANTICIPO
                                                    @endif
                                                </td>
                                                @php
                                                    if ($orden_pago->moneda_id == 1) {
                                                        $decimales = 0;
                                                    } else {
                                                        $decimales = 2;
                                                    }
                                                @endphp
                                                <td>{{number_format($orden_pago->monto_total, $decimales, ',', '.')}} {{ $orden_pago->moneda->codigo }}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($orden_pago->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($orden_pago->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Aprobada
                                                        @elseif ($orden_pago->estado == 'PA')
                                                            bg-info-subtle text-info text-uppercase"> Pagada
                                                        @elseif ($orden_pago->estado == 'AN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulada
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        if ($orden_pago->tipo == 'PR') {
                                                            $tipo = 'PR';
                                                        } else if ($orden_pago->tipo == 'GE') {
                                                            $tipo = 'GE';
                                                        } else if ($orden_pago->tipo == 'AN') {
                                                            $tipo = 'AN';
                                                        }
                                                    @endphp
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('ordenes_pagos.show', $orden_pago->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($orden_pago->estado == 'PE')
                                                        @can('reimprimir_pagos_ordenes')
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('ordenes_pagos.imprimir', ['id' => $orden_pago->id, 'tipo' => $tipo])}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Imprimir"><i class="ri-printer-fill"></i></a>
                                                        @endcan
                                                        @can('aprobar_pagos_ordenes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{$orden_pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                        @can('inactivar_pagos_ordenes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$orden_pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($orden_pago->estado == 'AP')
                                                        @can('reimprimir_pagos_ordenes')
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('ordenes_pagos.imprimir', ['id' => $orden_pago->id, 'tipo' => $tipo])}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Imprimir"><i class="ri-printer-fill"></i></a>
                                                        @endcan
                                                        @can('pagar_pagos_ordenes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#payModal-{{$orden_pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Realizar Pago"><i class="ri-file-list-3-line"></i></button>
                                                        @endcan
                                                        @can('desaprobar_pagos_ordenes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveModal-{{$orden_pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Desaprobar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($orden_pago->estado == 'AN')
                                                        @can('eliminar_pagos_ordenes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$orden_pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna órden de pago según tus parámetros de búsqueda.</p>
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
        @include('ordenes_pagos.scripts.index-scripts')
    @endsection
@endcan
