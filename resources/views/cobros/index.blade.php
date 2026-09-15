@can('ver_cobros')
    @extends('layouts.master')
    @section('title') Cobros @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Cobros @endslot
        @endcomponent

        @include('cobros.scripts.messages-scripts')
        @include('cobros.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Cobros</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="cobros-list">
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
                                <table class="table align-middle table-nowrap" id="cobros-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="venta">Factura de Venta</th>
                                            <th class="sort" data-sort="forma_pago">Forma de Pago</th>
                                            <th class="sort" data-sort="caja">Caja</th>
                                            <th class="sort" data-sort="monto">Monto</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($cobros as $cobro)
                                            <tr>
                                                <td>{{$cobro->id}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($cobro->created_at)->format('d/m/Y H:i:s')}}</td>
                                                <td class="venta">{{$cobro->venta->numero_factura}}</td>
                                                <td class="forma_pago">{{$cobro->formaPago->nombre}}</td>
                                                <td class="caja">
                                                    @if ($cobro->caja_id)
                                                        {{ $cobro->caja->nombre }}
                                                    @endif
                                                </td>
                                                <td class="monto">{{ number_format($cobro->monto, 0, ',', '.') }}</td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($cobro->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($cobro->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal-{{$cobro->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún cobro según tus parámetros de búsqueda.</p>
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
        @include('cobros.scripts.index-scripts')
    @endsection
@endcan
