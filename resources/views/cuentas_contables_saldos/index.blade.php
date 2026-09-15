@can('ver_cuentas_contables_saldos')
    @extends('layouts.master')
    @section('title') Cuentas Contables @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            table tbody tr td:nth-child(even) {
                background-color: rgb(235, 235, 235)!important;
            }
            .border-left {
                border-left: 3px solid black;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Cuentas Contables @endslot
        @endcomponent

        @include('cuentas_contables_saldos.scripts.messages-scripts')
        {{-- @include('cuentas_contables_saldos.modals.index-modals') --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Cuentas Contables</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <form action="{{route('cuentas_contables_saldos.index')}}" method="post" id="filtro-form">
                            @csrf
                            <div id="saldos-list">
                                <div class="row g-4 mb-3">
                                    <div class="col-lg-1 d-flex justify-content-sm-end">
                                        <label class="form-label mt-2" for="anho">Año:</label>
                                    </div>
                                    <div class="col-lg-1">
                                        <select class="selectpicker form-control" id="anho" name="anho" data-live-search="true">
                                            @foreach ($anhos as $anho)
                                                <option value="{{$anho}}" @if ($anho == $anho_seleccionado) selected @endif>{{$anho}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="d-flex justify-content-sm-end">
                                            <div class="search-box ms-2">
                                                <input type="text" class="form-control search" placeholder="Buscar...">
                                                <i class="ri-search-line search-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive table-card mt-3 mb-1">
                                    <table class="table align-middle table-borderless table-hover table-sm text-center" id="saldos-list">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort" data-sort="numero_cuenta" width="10%">N° Cuenta</th>
                                                <th class="sort" data-sort="cuenta" width="20%">Cuenta</th>
                                                @foreach ($meses as $key => $mes)
                                                    <th colspan="3" width="50%">{{$key}}</th>
                                                @endforeach
                                                <th colspan="3" width="20%">Total Año</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2"></th>
                                                @foreach ($meses as $mes)
                                                    <th class="sort" data-sort="debe_mes">Debe</th>
                                                    <th class="sort" data-sort="haber_mes">Haber</th>
                                                    <th  class="sort" data-sort="saldo_mes">Saldo</th>
                                                @endforeach
                                                <th class="sort" data-sort="debe_anho">Debe</th>
                                                <th class="sort" data-sort="haber_anho">Haber</th>
                                                <th  class="sort" data-sort="saldo_anho">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all table-striped-columns">
                                            @forelse ($saldos as $saldo)
                                                <tr>
                                                    <td class="numero_cuenta">{{$saldo->cuentaContable->cuenta}}</td>
                                                    <td class="cuenta">{{$saldo->cuentaContable->nombre}}</td>
                                                    @foreach ($meses as $mes => $cuentas)
                                                        @php
                                                            $debe = 0;
                                                            $haber = 0;
                                                            $saldo_valor = 0;
                                                            foreach ($cuentas as $cuenta_id => $cuenta) {
                                                                if ($saldo->cuenta_contable_id == $cuenta['cuenta_id']) {
                                                                    $debe = $cuenta['debe'];
                                                                    $haber = $cuenta['haber'];
                                                                    $saldo_valor = $cuenta['saldo'];
                                                                }
                                                            }
                                                        @endphp
                                                        <td class="debe_mes">{{ number_format($debe, 0 , ',', '.') }}</td>
                                                        <td class="haber_mes">{{ number_format($haber, 0 , ',', '.') }}</td>
                                                        <td class="saldo_mes @if ($saldo_valor < 0) text-danger @endif">{{ number_format($saldo_valor, 0 , ',', '.') }}</td>
                                                    @endforeach
                                                    @php
                                                        $debe_2 = 0;
                                                        $haber_2 = 0;
                                                        $saldo_valor_2 = 0;
                                                        foreach ($total_anho as $anho) {
                                                            if ($saldo->cuenta_contable_id == $anho['cuenta_id']) {
                                                                $debe_2 = $anho['debe'];
                                                                $haber_2 = $anho['haber'];
                                                                $saldo_valor_2 = $anho['saldo'];
                                                            }
                                                        }
                                                    @endphp
                                                    <td class="debe_anho border-left">{{ number_format($debe_2, 0 , ',' ,'.') }}</td>
                                                    <td class="haber_anho">{{ number_format($haber_2, 0 , ',' ,'.') }}</td>
                                                    <td class="saldo_anho @if ($saldo_valor_2 < 0) text-danger @endif">{{ number_format($saldo_valor_2, 0 , ',' ,'.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">No existen movimientos para el año seleccionado</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="noresults" style="display: none">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                            <h5 class="mt-2">Sin resultados.</h5>
                                            <p class="text-muted mb-0">No pudimos encontrar ningún cuenta_contable según tus parámetros de búsqueda.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="d-flex justify-content-end">
                                    <div class="pagination-wrap hstack gap-2">
                                        <a class="page-item pagination-prev disabled"><</a>
                                        <ul class="pagination listjs-pagination mb-0"></ul>
                                        <a class="page-item pagination-next">></a>
                                    </div>
                                </div> --}}
                            </div>
                        </form>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('cuentas_contables_saldos.scripts.index-scripts')
    @endsection
@endcan
