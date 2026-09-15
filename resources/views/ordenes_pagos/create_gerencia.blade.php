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
            <form action="{{route('ordenes_pagos.store_gerencia')}}" method="post" id="store-form">
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
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="proveedor">Proveedor <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('proveedor') is-invalid @enderror" id="proveedor" name="proveedor" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{$proveedor->id}}" @if (old('proveedor') == strval($proveedor->id)) selected @endif data-subtext="{{$proveedor->ruc}}">{{$proveedor->razon_social}}</option>
                                        @endforeach
                                    </select>
                                    @error('proveedor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="moneda">Moneda <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('moneda') is-invalid @enderror" id="moneda" name="moneda" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($monedas as $moneda)
                                            <option value="{{$moneda->id}}" @if (old('moneda') == strval($moneda->id)) selected @endif>{{$moneda->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('moneda')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="monto">Monto <span class="text-danger">(*)</span></label>
                                    <input class="form-control @error('monto_guaranies') is-invalid @enderror" id="monto_guaranies" name="monto_guaranies" value="{{old('monto_guaranies')}}">
                                    <input class="form-control d-none @error('monto_dolares') is-invalid @enderror" id="monto_dolares" name="monto_dolares" value="{{old('monto_dolares')}}">
                                    @error('monto_guaranies')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                    @error('monto_dolares')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('unidad_negocio') is-invalid @enderror" id="unidad_negocio" name="unidad_negocio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($unidades_negocios as $unidad_negocio)
                                            <option value="{{$unidad_negocio->id}}" @if (old('unidad_negocio') == strval($unidad_negocio->id)) selected @endif>{{$unidad_negocio->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('unidad_negocio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Subunidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('subunidad_negocio') is-invalid @enderror" id="subunidad_negocio" name="subunidad_negocio" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('subunidad_negocio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="cuenta_contable">Cuenta Contable <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('cuenta_contable') is-invalid @enderror" id="cuenta_contable" name="cuenta_contable" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_contables as $cuenta_contable)
                                            <option value="{{$cuenta_contable->id}}" @if (old('cuenta_contable') == strval($cuenta_contable->id)) selected @endif data-subtext="{{$cuenta_contable->cuenta}}">{{$cuenta_contable->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_contable')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="concepto">Concepto <span class="text-danger">(*)</span></label>
                                    <textarea class="form-control @error('concepto') is-invalid @enderror" id="concepto" name="concepto" cols="30" rows="2">{{old('concepto')}}</textarea>
                                    @error('concepto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
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
        @include('ordenes_pagos.scripts.create_gerencia-scripts')
    @endsection
@endcan
