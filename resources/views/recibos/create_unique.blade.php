@can('crear_recibos')
    @extends('layouts.master')
    @section('title') Agregar Recibo @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Recibos @endslot
            @slot('title') Agregar Recibo  @endslot
        @endcomponent

        @include('recibos.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('recibos.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo recibo</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{$fecha_hoy}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="numero_recibo">N° Recibo</label>
                                    <input type="text" class="form-control text-center" id="numero_recibo" value="{{$numero_recibo}}" readonly>
                                    <input type="hidden" name="numero" value="{{$numero}}">
                                </div>
                                <div class="col-lg-8 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="caja">Caja</label>
                                        <input type="text" class="form-control text-center" id="caja" name="caja" value="{{$caja->caja->nombre}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="cliente">Cliente</label>
                                    <input type="text" class="form-control" id="cliente" value="{{ $venta->cliente->nombre }}" readonly>
                                    <input type="hidden" name="cliente" value="{{ $venta->cliente->id }}">
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cliente">N° Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$venta->cliente->numero_documento}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="forma_pago">Forma de Pago <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('forma_pago') is-invalid @enderror" id="forma_pago" name="forma_pago" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($formas_pagos as $forma_pago)
                                            <option value="{{ $forma_pago->id }}" @if (old('forma_pago') == strval($forma_pago->id)) selected @endif>{{$forma_pago->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('forma_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-debito">
                                    <label class="form-label" for="banco_debito">Banco <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('banco_debito') is-invalid @enderror" id="banco_debito" name="banco_debito" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($bancos as $banco)
                                            <option value="{{$banco->id}}" @if (old('banco_debito') == strval($banco->id)) selected @endif>{{$banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('banco_debito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-debito">
                                    <label class="form-label" for="numero_transaccion_debito">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_debito') is-invalid @enderror" id="numero_transaccion_debito" name="numero_transaccion_debito" value="{{old('numero_transaccion_debito')}}">
                                    @error('numero_transaccion_debito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-debito">
                                    <label class="form-label" for="fecha_transaccion_debito">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_nacimiento') is-invalid @enderror" id="fecha_transaccion_debito" name="fecha_transaccion_debito" value="{{old('fecha_transaccion_debito')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_transaccion_debito')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-credito">
                                    <label class="form-label" for="banco_credito">Banco <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('banco_credito') is-invalid @enderror" id="banco_credito" name="banco_credito" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($bancos as $banco)
                                            <option value="{{$banco->id}}" @if (old('banco_credito') == strval($banco->id)) selected @endif>{{$banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('banco_credito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-credito">
                                    <label class="form-label" for="numero_transaccion_credito">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_credito') is-invalid @enderror" id="numero_transaccion_credito" name="numero_transaccion_credito" value="{{old('numero_transaccion_credito')}}">
                                    @error('numero_transaccion_credito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-credito">
                                    <label class="form-label" for="fecha_transaccion_credito">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_nacimiento') is-invalid @enderror" id="fecha_transaccion_credito" name="fecha_transaccion_credito" value="{{old('fecha_transaccion_credito')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_transaccion_credito')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-transferencia">
                                    <label class="form-label" for="banco_transferencia">Banco Origen <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('banco_transferencia') is-invalid @enderror" id="banco_transferencia" name="banco_transferencia" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($bancos as $banco)
                                            <option value="{{$banco->id}}" @if (old('banco_transferencia') == strval($banco->id)) selected @endif>{{$banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('banco_transferencia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-transferencia">
                                    <label class="form-label" for="cuenta_bancaria_transferencia">Cuenta Destino <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_bancaria_transferencia') is-invalid @enderror" id="cuenta_bancaria_transferencia" name="cuenta_bancaria_transferencia" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_bancarias as $cuenta_bancaria)
                                            <option value="{{$cuenta_bancaria->id}}" @if (old('cuenta_bancaria_transferencia') == strval($cuenta_bancaria->id)) selected @endif data-subtext="{{ $cuenta_bancaria->numero_cuenta }}">{{$cuenta_bancaria->banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_bancaria_transferencia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-transferencia">
                                    <label class="form-label" for="numero_transaccion_transferencia">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_transferencia') is-invalid @enderror" id="numero_transaccion_transferencia" name="numero_transaccion_transferencia" value="{{old('numero_transaccion_transferencia')}}">
                                    @error('numero_transaccion_transferencia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-transferencia">
                                    <label class="form-label" for="fecha_transaccion_transferencia">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_nacimiento') is-invalid @enderror" id="fecha_transaccion_transferencia" name="fecha_transaccion_transferencia" value="{{old('fecha_transaccion_transferencia')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_transaccion_transferencia')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-deposito">
                                    <label class="form-label" for="cuenta_bancaria_deposito">Cuenta Destino <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_bancaria_deposito') is-invalid @enderror" id="cuenta_bancaria_deposito" name="cuenta_bancaria_deposito" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_bancarias as $cuenta_bancaria)
                                            <option value="{{$cuenta_bancaria->id}}" @if (old('cuenta_bancaria_deposito') == strval($cuenta_bancaria->id)) selected @endif data-subtext="{{ $cuenta_bancaria->numero_cuenta }}">{{$cuenta_bancaria->banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_bancaria_deposito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-deposito">
                                    <label class="form-label" for="numero_transaccion_deposito">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_deposito') is-invalid @enderror" id="numero_transaccion_deposito" name="numero_transaccion_deposito" value="{{old('numero_transaccion_deposito')}}">
                                    @error('numero_transaccion_deposito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none div-deposito">
                                    <label class="form-label" for="fecha_transaccion_deposito">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_nacimiento') is-invalid @enderror" id="fecha_transaccion_deposito" name="fecha_transaccion_deposito" value="{{old('fecha_transaccion_deposito')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_transaccion_deposito')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalle del Recibo</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-3 mb-2 text-center">
                                                <label class="form-label" for="venta-0">Factura</label>
                                                <input type="text" class="form-control text-center" id="venta-0" value="{{ $venta->numero_factura }}" readonly>
                                                <input type="hidden" name="detalles[0][venta]" value="{{ $venta->id }}">
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center">
                                                <label class="form-label" for="monto_pendiente-0">Saldo de Factura</label>
                                                <input type="text" class="form-control text-center" id="monto_pendiente-0" value="{{number_format($venta->saldo, 0, ',', '.')}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center">
                                                <label class="form-label" for="monto-0">Monto a Cobrar <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center monto-0 monto @error('detalles.0.monto') is-invalid @enderror" id="monto-0" name="detalles[0][monto]" value="{{old('detalles.0.monto')}}">
                                                @error('detalles.0.monto')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 d-flex justify-content-end mb-5">
                            <div class="col-lg-2 text-center">
                                <label class="form-label" for="total">Total a Cobrar</label>
                                <input type="text" class="form-control text-center total" id="total" name="total" value="{{old('total', 0)}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Cobrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('recibos.scripts.create_unique-scripts')
    @endsection
@endcan
