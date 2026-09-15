@can('ver_pagos')
    @extends('layouts.master')
    @section('title') Pagos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Pagos @endslot
        @endcomponent

        @include('pagos.scripts.messages-scripts')
        @include('pagos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Pagos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="pagos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="pagos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="venta">OP N°</th>
                                            <th class="sort" data-sort="orden_pago">Forma de Pago</th>
                                            <th class="sort" data-sort="aprobado_por">Aprobado Por</th>
                                            <th class="sort" data-sort="monto">Monto</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($pagos as $pago)
                                            <tr>
                                                <td>{{$pago->id}}</td>
                                                <td class="fecha">
                                                    @if ($pago->fecha)
                                                        {{Carbon\Carbon::parse($pago->created_at)->format('d/m/Y H:i:s')}}
                                                    @else
                                                        ---
                                                    @endif
                                                </td>
                                                <td class="orden_pago">{{$pago->orden_pago_id}}</td>
                                                <td class="forma_pago">{{$pago->formaPago->nombre}}</td>
                                                <td class="aprobado_por">
                                                    @if ($pago->ordenPago->aprobado_por_id)
                                                        {{ $pago->ordenPago->aprobadoPor->name }}
                                                    @else
                                                        ---
                                                    @endif
                                                </td>
                                                @php
                                                    if ($pago->ordenPago->moneda_id == 1) {
                                                        $decimales = 0;
                                                    } else {
                                                        $decimales = 2;
                                                    }
                                                @endphp
                                                <td class="monto">{{ number_format($pago->monto, $decimales, ',', '.') }} {{$pago->ordenPago->moneda->codigo}}</td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($pago->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($pago->estado == 'PA')
                                                            bg-success-subtle text-success text-uppercase"> Pagado
                                                        @elseif ($pago->estado == 'AN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal-{{$pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                    @if ($pago->estado == 'PE')
                                                        @can('crear_pagos')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#storeModal-{{$pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Realizar Pago"><i class="ri-file-list-3-line"></i></button>
                                                        @endcan
                                                    @else
                                                        @can('anular_pagos')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún pago según tus parámetros de búsqueda.</p>
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
        @include('pagos.scripts.index-scripts')
    @endsection
@endcan
