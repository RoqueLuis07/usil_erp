@can('ver_notas_creditos')
    @extends('layouts.master')
    @section('title') Notas de Crédito @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Notas de Crédito @endslot
        @endcomponent

        @include('notas_creditos.scripts.messages-scripts')
        @include('notas_creditos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Notas de Crédito</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="notas_creditos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_notas_creditos')
                                        <a type="button" class="btn btn-success" href="{{route('notas_creditos.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="notas_creditos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="cliente">Cliente</th>
                                            <th class="sort" data-sort="numero_nota_credito">N° Nota Crédito</th>
                                            <th class="sort" data-sort="numero_venta">N° Factura Aplicada</th>
                                            <th class="sort" data-sort="numero_venta_cobrada">N° Factura Cobrada</th>
                                            <th class="sort" data-sort="monto">Monto</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($notas_creditos as $nota_credito)
                                            <tr>
                                                <td>{{$nota_credito->id}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($nota_credito->fecha)->format('d/m/Y H:i')}}</td>
                                                <td class="cliente">{{$nota_credito->cliente->nombre}} <span class="text-muted">{{$nota_credito->cliente->numero_documento}}</span></td>
                                                <td class="numero_nota_credito">{{$nota_credito->numero_nota_credito}}</td>
                                                <td class="numero_venta">{{$nota_credito->venta->numero_factura}}</td>
                                                <td class="numero_venta_cobrada">
                                                    @if ($nota_credito->estado == 'UT')
                                                        {{$nota_credito->cobro->venta->numero_factura}}
                                                    @elseif ($nota_credito->estado == 'AC')
                                                        PENDIENTE
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="monto">{{number_format($nota_credito->monto_total, 0,',', '.')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($nota_credito->estado == 'AC')
                                                            bg-warning-subtle text-warning text-uppercase"> Activo
                                                        @elseif ($nota_credito->estado == 'UT')
                                                            bg-success-subtle text-success text-uppercase"> Utilizado
                                                        @elseif ($nota_credito->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal-{{$nota_credito->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                    @if ($nota_credito->estado == 'AC')
                                                        @can('anular_notas_creditos')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$nota_credito->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($nota_credito->estado == 'IN')
                                                        @can('eliminar_notas_creditos')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$nota_credito->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna nota de crédito según tus parámetros de búsqueda.</p>
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
        @include('notas_creditos.scripts.index-scripts')
    @endsection
@endcan
