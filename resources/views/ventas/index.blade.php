@can('ver_ventas')
    @extends('layouts.master')
    @section('title') Ventas @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Ventas @endslot
        @endcomponent

        @include('ventas.scripts.messages-scripts')
        @include('ventas.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Ventas</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="ventas-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_ventas')
                                        <a type="button" class="btn btn-success" href="{{route('ventas.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="ventas-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="cliente">Cliente</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="numero_factura">N° Factura</th>
                                            <th class="sort" data-sort="monto">Monto</th>
                                            <th class="sort" data-sort="tipo">Tipo</th>
                                            <th class="sort" data-sort="tipo">Saldo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($ventas as $venta)
                                            <tr>
                                                <td>{{$venta->id}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i')}}</td>
                                                <td class="cliente">{{$venta->cliente->nombre}} <span class="text-muted">{{$venta->cliente->numero_documento}}</span></td>
                                                <td class="alumno">@if ($venta->alumno_id) {{$venta->alumno->primer_nombre}} {{$venta->alumno->segundo_nombre}} {{$venta->alumno->tecer_nombre}} {{$venta->alumno->primer_apellido}} {{$venta->alumno->segundo_apellido}} <span class="text-muted">{{$venta->alumno->numero_documento}} </span> @endif</td>
                                                <td class="numero_factura">{{$venta->numero_factura}}</td>
                                                <td class="monto">{{number_format($venta->monto_total, 0,',', '.')}}</td>
                                                <td class="tipo">
                                                    @if ($venta->forma_pago == 'CO')
                                                        CONTADO
                                                    @else
                                                        CREDITO
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($venta->saldo == 0)
                                                        0
                                                    @else
                                                        {{number_format($venta->saldo, 0,',', '.')}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($venta->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Facturado
                                                        @elseif ($venta->estado == 'CO')
                                                            bg-success-subtle text-success text-uppercase"> Cobrado
                                                        @elseif ($venta->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulado
                                                        @elseif ($venta->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> A Cobrar
                                                        @elseif ($venta->estado == 'NC')
                                                            bg-success-subtle text-success text-uppercase"> Nota Crédito
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('ventas.show', $venta->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($venta->estado == 'AC' || $venta->estado == 'PE')
                                                        @if ($venta->estado == 'PE')
                                                            @can('crear_recibos')
                                                                <a class="btn btn-sm btn-success" href="{{ route('recibos.create_unique', $venta->id) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Cobrar Venta"><i class="ri-hand-coin-fill"></i></a>
                                                            @endcan
                                                        @endif
                                                        @can('anular_ventas')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$venta->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($venta->estado == 'PE')
                                                        @can('crear_recibos')
                                                            <a class="btn btn-sm btn-success" href="#" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Cobrar Venta"><i class="ri-hand-coin-fill"></i></a>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_ventas')
                                                        @if ($venta->estado == 'IN')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$venta->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                        @endif
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna venta según tus parámetros de búsqueda.</p>
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
        @include('ventas.scripts.index-scripts')
    @endsection
@endcan
