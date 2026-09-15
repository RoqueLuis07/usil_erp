@can('ver_asientos_contables')
    @extends('layouts.master')
    @section('title') Asientos Contables @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr-month-select.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Asientos Contables @endslot
        @endcomponent

        @include('asientos_contables.scripts.messages-scripts')
        @include('asientos_contables.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Asientos Contables</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="asientos_contables-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_asientos_contables')
                                        <a type="button" class="btn btn-success" href="{{route('asientos_contables.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                    {{-- @can('renumerar_asientos_contables') --}}
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#renumerarModal"></i>Renumerar Asientos</button>
                                    {{-- @endcan --}}
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
                                <table class="table align-middle text-center" id="asientos_contables-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">N°</th>
                                            <th class="sort" data-sort="fecha" width="5%">Fecha</th>
                                            <th class="sort" data-sort="origen" width="5%">Origen</th>
                                            <th class="sort" data-sort="numero_cuenta" width="10%">N° Cuenta</th>
                                            <th class="sort" data-sort="cuenta_contable" width="10%">Cuenta</th>
                                            <th class="sort" data-sort="descripcion" width="30%">Descripción</th>
                                            <th class="sort" data-sort="debe" width="10%">Debe</th>
                                            <th class="sort" data-sort="haber" width="10%">Haber</th>
                                            <th width="5%">Moneda</th>
                                            <th width="10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($asientos as $detalle)
                                            <tr>
                                                <td>{{$detalle->asiento->numero}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($detalle->asiento->fecha)->format('d/m/Y')}}</td>
                                                <td class="origen">{{$detalle->asiento->origen}}</td>
                                                <td class="numero_cuenta">{{$detalle->cuentaContable->cuenta}}</td>
                                                <td class="cuenta_contable">{{$detalle->cuentaContable->nombre}}</td>
                                                <td class="descripcion">{{$detalle->descripcion}}</td>
                                                <td class="debe">@if ($detalle->debe) {{number_format($detalle->debe, 0, ',', '.')}} @else ----- @endif</td>
                                                <td class="haber">@if ($detalle->haber) {{number_format($detalle->haber, 0, ',', '.')}} @else ----- @endif</td>
                                                <td>{{$detalle->asiento->moneda->codigo}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('asientos_contables.show', $detalle->asiento->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($detalle->asiento->origen == 'MANUAL')
                                                        {{-- @can('editar_asientos_contables')
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('asientos_contables.edit', $detalle->asiento->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                        @endcan --}}
                                                        @can('eliminar_asientos_contables')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún asiento según tus parámetros de búsqueda.</p>
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr-month-select.js') }}"></script>
        @include('asientos_contables.scripts.index-scripts')
    @endsection
@endcan
