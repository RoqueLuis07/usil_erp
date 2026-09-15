@can('ver_recibos')
    @extends('layouts.master')
    @section('title') Recibos @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Recibos @endslot
        @endcomponent

        @include('recibos.scripts.messages-scripts')
        @include('recibos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Recibos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="recibos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_recibos')
                                        <a type="button" class="btn btn-success" href="{{route('recibos.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="recibos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N°</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="cliente">Cliente</th>
                                            <th class="sort" data-sort="forma_pago">Forma de Pago</th>
                                            <th class="sort" data-sort="monto">Monto</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($recibos as $recibo)
                                            <tr>
                                                <td>{{str_pad($recibo->numero, 7, '0', STR_PAD_LEFT)}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($recibo->fecha)->format('d/m/Y H:i')}}</td>
                                                <td class="cliente">{{$recibo->cliente->nombre}} <span class="text-muted">{{$recibo->cliente->numero_documento}}</span></td>
                                                <td class="forma_pago">{{$recibo->formaPago->nombre}}</td>
                                                <td class="monto">{{number_format($recibo->monto_total, 0,',', '.')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($recibo->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($recibo->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Anulado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('recibos.show', $recibo->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($recibo->estado == 'AC')
                                                        @can('anular_recibos')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$recibo->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_recibos')
                                                        @if ($recibo->estado == 'IN')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$recibo->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningun recibo según tus parámetros de búsqueda.</p>
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
        @include('recibos.scripts.index-scripts')
    @endsection
@endcan
