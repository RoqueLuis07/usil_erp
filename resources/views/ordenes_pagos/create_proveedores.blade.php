@can('crear_pagos_ordenes')
    @extends('layouts.master')
    @section('title') Agregar Orden de Pago @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ordenes de Pagos @endslot
            @slot('title') Agregar Orden de Pago  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('ordenes_pagos.store_proveedor')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva órden de pago</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="numero_orden">N° OP</label>
                                    <input type="text" class="form-control text-center" id="numero_orden" value="{{$numero_orden}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::today()->format('d/m/y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="forma_pago">Forma de Pago <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('forma_pago') is-invalid @enderror" id="forma_pago" name="forma_pago" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($formas_pagos as $forma_pago)
                                            <option value="{{$forma_pago->id}}" @if (old('forma_pago') == strval($forma_pago->id)) selected @endif>{{$forma_pago->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('forma_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3 @if (old('forma_pago') != 1 && old('forma_pago') != 5) d-none @endif" id="div-caja">
                                    <label class="form-label" for="caja">Caja <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('caja') is-invalid @enderror" id="caja" name="caja" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cajas as $caja)
                                            <option value="{{$caja->id}}" @if (old('caja') == strval($caja->id)) selected @endif>{{$caja->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('caja')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 @if (old('forma_pago') != 4 && old('forma_pago') != 7) d-none @endif" id="div-cuenta_bancaria">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('cuenta_bancaria') is-invalid @enderror" id="cuenta_bancaria" name="cuenta_bancaria" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_bancarias as $cuenta_bancaria)
                                            <option value="{{$cuenta_bancaria->id}}" @if (old('cuenta_bancaria') == strval($cuenta_bancaria->id)) selected @endif data-subtext="{{ $cuenta_bancaria->numero_cuenta }}">{{$cuenta_bancaria->banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_bancaria')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 @if (old('forma_pago') != 7) d-none @endif" id="div-numero_cheque">
                                    <label class="form-label" for="numero_cheque">N° de Cheque <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_cheque') is-invalid @enderror" id="numero_cheque" name="numero_cheque" value="{{ old('numero_cheque') }}">
                                    @error('numero_cheque')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 @if (old('forma_pago') != 7) d-none @endif" id="div-numero_serie">
                                    <label class="form-label" for="numero_serie">N° de Serie <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_serie') is-invalid @enderror" id="numero_serie" name="numero_serie" value="{{ old('numero_serie') }}">
                                    @error('numero_serie')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Factura de Compra</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="compra">Factura de Compra <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control @error('compra') is-invalid @enderror" id="compra" name="compra" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($compras as $compra)
                                                        <option value="{{$compra->id}}" @if (old('compra') == strval($compra->id)) selected @endif data-subtext="{{ $compra->proveedor->razon_social }}">{{$compra->numero_factura}}</option>
                                                    @endforeach
                                                </select>
                                                @error('compra')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-1 mb-2 text-center">
                                                <label class="form-label" for="fecha_factura">Fecha</label>
                                                <input type="text" class="form-control text-center" id="fecha_factura" name="fecha_factura" value="{{ old('fecha_factura') }}" readonly>
                                            </div>
                                            <div class="col-lg-3 mb-2 text-center">
                                                <label class="form-label" for="proveedor">Proveedor</label>
                                                <input type="text" class="form-control text-center" id="proveedor" name="proveedor" value="{{ old('proveedor') }}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center">
                                                <label class="form-label" for="ruc_proveedor">R.U.C.</label>
                                                <input type="text" class="form-control text-center" id="ruc_proveedor" name="ruc_proveedor" value="{{ old('ruc_proveedor') }}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center">
                                                <label class="form-label" for="numero_factura">N° de Factura</label>
                                                <input type="text" class="form-control text-center" id="numero_factura" name="numero_factura" value="{{ old('numero_factura') }}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center">
                                                <label class="form-label" for="condicion">Condición</label>
                                                <input type="text" class="form-control text-center" id="condicion" name="condicion" value="{{ old('condicion') }}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center">
                                                <label class="form-label" for="monto_total">Monto Total</label>
                                                <input type="text" class="form-control text-center" id="monto_total" name="monto_total" value="{{ old('monto_total') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ordenes_pagos.scripts.create_proveedores-scripts')
    @endsection
@endcan
