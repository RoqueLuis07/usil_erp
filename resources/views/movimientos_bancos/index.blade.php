@can('ver_movimientos_cuentas')
    @extends('layouts.master')
    @section('title') Movimientos de Bancos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Movimientos de Bancos @endslot
        @endcomponent

        @include('movimientos_bancos.scripts.messages-scripts')
        @include('movimientos_bancos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Movimientos de Bancos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="movimientos_bancos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_movimientos_cuentas')
                                        <a type="button" class="btn btn-success" href="{{route('movimientos_bancos.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="movimientos_bancos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="tipo_movimiento">Tipo Mov.</th>
                                            <th class="sort" data-sort="cuenta_origen">Cuenta Origen</th>
                                            <th></th>
                                            <th class="sort" data-sort="cuenta_destino">Cuenta Destino</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th>Monto Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($movimientos_bancos as $movimiento)
                                            <tr>
                                                <td class="id">{{$movimiento->id}}</td>
                                                <td class="tipo_movimiento">{{$movimiento->tipoMovimiento->nombre}}</td>
                                                @if ($movimiento->cuenta_bancaria_origen_id)
                                                    <td class="cuenta_origen">{{$movimiento->cuentaBancariaOrigen->banco->nombre}} <span class="text-muted">{{$movimiento->cuentaBancariaOrigen->numero_cuenta}}</span></td>
                                                @else
                                                    <td class="cuenta_origen">---</td>
                                                @endif
                                                <td>
                                                    @if ($movimiento->sentido == 'I')
                                                        <i class="ri-arrow-left-line"></i>
                                                    @else
                                                        <i class="ri-arrow-right-line"></i>
                                                    @endif
                                                </td>
                                                @if ($movimiento->cuenta_bancaria_destino_id)
                                                    <td class="cuenta_destino">{{$movimiento->cuentaBancariaDestino->banco->nombre}} <span class="text-muted">{{$movimiento->cuentaBancariaDestino->numero_cuenta}}</span></td>
                                                @else
                                                    <td class="cuenta_destino">---</td>
                                                @endif
                                                <td class="fecha">{{Carbon\Carbon::parse($movimiento->fecha)->format('d/m/Y H:i:s')}}</td>
                                                <td>
                                                    @if ($movimiento->cuenta_bancaria_origen_id)
                                                        @if ($movimiento->cuentaBancariaOrigen->moneda_id == 1)
                                                            {{number_format($movimiento->monto, 0, ',', '.')}} {{$movimiento->cuentaBancariaOrigen->moneda->codigo}}
                                                        @else
                                                            {{number_format($movimiento->monto, 2, ',', '.')}} {{$movimiento->cuentaBancariaOrigen->moneda->codigo}}
                                                        @endif
                                                    @elseif ($movimiento->cuenta_bancaria_destino_id)
                                                        @if ($movimiento->cuentaBancariaDestino->moneda_id == 1)
                                                            {{number_format($movimiento->monto, 0, ',', '.')}} {{$movimiento->cuentaBancariaDestino->moneda->codigo}}
                                                        @else
                                                            {{number_format($movimiento->monto, 2, ',', '.')}} {{$movimiento->cuentaBancariaDestino->moneda->codigo}}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($movimiento->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($movimiento->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @elseif ($movimiento->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulado
                                                        @elseif ($movimiento->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal-{{$movimiento->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                    @if ($movimiento->estado == 'PE')
                                                        @can('editar_movimientos_cuentas')
                                                            <a type="button" class="btn btn-sm btn-info" href="{{route('movimientos_bancos.edit', $movimiento->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                        @endcan
                                                        @can('aprobar_movimientos_cuentas')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{$movimiento->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                        @can('rechazar_movimientos_cuentas')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{$movimiento->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rechazar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($movimiento->estado == 'RE')
                                                        @can('anular_rechazo_movimientos_cuentas')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#unrejectModal-{{$movimiento->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Rechazar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($movimiento->estado == 'AP')
                                                        @can('anular_aprobacion_movimientos_cuentas')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveModal-{{$movimiento->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Aprobación"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_movimientos_cuentas')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$movimiento->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún movimiento según tus parámetros de búsqueda.</p>
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
        @include('movimientos_bancos.scripts.index-scripts')
    @endsection
@endcan
