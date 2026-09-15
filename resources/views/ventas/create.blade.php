@can('crear_ventas')
    @extends('layouts.master')
    @section('title') Agregar Venta @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ventas @endslot
            @slot('title') Agregar Venta  @endslot
        @endcomponent

        @include('ventas.scripts.messages-scripts')
        <div class="row">
            <form action="{{route('ventas.store')}}" method="post" id="store-form">
                @csrf
                @include('ventas.modals.create-modals')
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva venta</h4>
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
                                    <label class="form-label" for="numero_factura">N° Factura</label>
                                    <input type="text" class="form-control text-center" id="numero_factura" name="numero_factura" value="{{$numero_factura}}" readonly>
                                </div>
                                <div class="col-lg-8 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="caja">Caja</label>
                                        <input type="text" class="form-control text-center" id="caja" name="caja" value="{{$caja->caja->nombre}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="cliente">Cliente <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cliente') is-invalid @enderror" id="cliente" name="cliente" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{$cliente->id}}" @if (old('cliente') == strval($cliente->id)) selected @endif data-subtext="{{$cliente->numero_documento}}">{{$cliente->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cliente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="forma_pago">Forma de Pago <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('forma_pago') is-invalid @enderror" id="forma_pago" name="forma_pago" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="CO" @if (old('forma_pago') == 'CO') selected @endif>CONTADO</option>
                                        <option value="CR" @if (old('forma_pago') == 'CR') selected @endif>CREDITO</option>
                                    </select>
                                    @error('forma_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none" id="div-credito_a">
                                    <label class="form-label" for="credito_a">Crédito a <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('credito_a') is-invalid @enderror" id="credito_a" name="credito_a" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="7" @if (old('credito_a') == '7') selected @endif>7 DIAS</option>
                                        <option value="15" @if (old('credito_a') == '15') selected @endif>15 DIAS</option>
                                        <option value="30" @if (old('credito_a') == '30') selected @endif>30 DIAS</option>
                                        <option value="45" @if (old('credito_a') == '45') selected @endif>45 DIAS</option>
                                        <option value="60" @if (old('credito_a') == '60') selected @endif>60 DIAS</option>
                                        <option value="90" @if (old('credito_a') == '90') selected @endif>90 DIAS</option>
                                        <option value="120" @if (old('credito_a') == '120') selected @endif>120 DIAS</option>
                                    </select>
                                    @error('credito_a')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none" id="div-metodo_pago">
                                    <label class="form-label" for="metodo_pago">Método de Pago <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('metodo_pago') is-invalid @enderror" id="metodo_pago" name="metodo_pago" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($formas_pagos as $forma_pago)
                                            <option value="{{$forma_pago->id}}" @if (old('metodo_pago') == strval($forma_pago->id)) selected @endif>{{$forma_pago->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('metodo_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none" id="div-nota_credito">
                                    <label class="form-label" for="cuenta_bancaria_deposito">Nota de Crédito <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('nota_credito') is-invalid @enderror" id="nota_credito" name="nota_credito" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('nota_credito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none" id="div-monto_nota_credito">
                                    <label class="form-label" for="monto_nota_credito">Monto NC</label>
                                    <input type="text" class="form-control text-center" id="monto_nota_credito" name="monto_nota_credito" value="{{old('monto_nota_credito')}}" readonly>
                                </div>
                            </div>
                            <div class="row d-none" id="row-debito">
                                <div class="col-lg-3 mb-3">
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
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="numero_transaccion_debito">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_debito') is-invalid @enderror" id="numero_transaccion_debito" name="numero_transaccion_debito" value="{{old('numero_transaccion_debito')}}">
                                    @error('numero_transaccion_debito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
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
                            </div>
                            <div class="row d-none" id="row-credito">
                                <div class="col-lg-3 mb-3">
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
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="numero_transaccion_credito">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_credito') is-invalid @enderror" id="numero_transaccion_credito" name="numero_transaccion_credito" value="{{old('numero_transaccion_credito')}}">
                                    @error('numero_transaccion_credito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
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
                            </div>
                            <div class="row d-none" id="row-transferencia">
                                <div class="col-lg-3 mb-3">
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
                                <div class="col-lg-3 mb-3">
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
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="numero_transaccion_transferencia">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_transferencia') is-invalid @enderror" id="numero_transaccion_transferencia" name="numero_transaccion_transferencia" value="{{old('numero_transaccion_transferencia')}}">
                                    @error('numero_transaccion_transferencia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
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
                            </div>
                            <div class="row d-none" id="row-deposito">
                                <div class="col-lg-3 mb-3">
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
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="numero_transaccion_deposito">N° de Transacción <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center numero_transaccion @error('numero_transaccion_deposito') is-invalid @enderror" id="numero_transaccion_deposito" name="numero_transaccion_deposito" value="{{old('numero_transaccion_deposito')}}">
                                    @error('numero_transaccion_deposito')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
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
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('unidad_negocio') is-invalid @enderror" id="unidad_negocio" name="unidad_negocio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($unidades_negocios as $unidad_negocio)
                                            <option value="{{ $unidad_negocio->id }}" @if (old('unidad_negocio') == strval($unidad_negocio->id)) selected @endif>{{$unidad_negocio->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="subunidad_negocio">Subunidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('subunidad_negocio') is-invalid @enderror" id="subunidad_negocio" name="subunidad_negocio" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Venta</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-2 mb-2 text-center" id="div-alumno-0">
                                                <label class="form-label label-alumno" for="alumno-0">Alumno <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control alumno-0 alumno @error('detalles.0.alumno') is-invalid @enderror" id="alumno-0" name="detalles[0][alumno]" data-live-search="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($alumnos as $alumno)
                                                        <option value="{{ $alumno->id }}" @if (old('detalles.0.alumno') == strval ($alumno->id)) selected @endif data-subtext="{{ $alumno->numero_documento }}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.alumno')
                                                    <span class="invalidad-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-3 mb-2 text-center" id="div-articulo-0">
                                                <label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control articulo-0 articulo @error('detalles.0.articulo') is-invalid @enderror" id="articulo-0" name="detalles[0][articulo]" data-live-search="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($articulos as $articulo)
                                                        <option value="{{$articulo->id}}" @if (old('detalles.0.articulo') == strval($articulo->id)) selected @endif >{{$articulo->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.articulo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center" id="div-centro_costo-0">
                                                <label class="form-label label-centro_costo">CC1 <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control centro_costo-0 centro_costo @error('detalles.0.centro_costo') is-invalid @enderror" id="centro_costo-0" name="detalles[0][centro_costo]" data-live-search="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($centros_costos as $centro_costo)
                                                        <option value="{{$centro_costo->id}}" @if (old('detalles.0.centro_costo') == strval($centro_costo->id)) selected @endif >{{$centro_costo->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.centro_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center" id="div-subcentro_costo-0">
                                                <label class="form-label label-subcentro_costo">CC2 <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control subcentro_costo-0 subcentro_costo @error('detalles.0.subcentro_costo') is-invalid @enderror" id="subcentro_costo-0" name="detalles[0][subcentro_costo]" data-live-search="true" data-id="0" disabled>
                                                    <option value="" selected disabled>Seleccionar...</option>

                                                </select>
                                                @error('detalles.0.subcentro_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center" id="div-precio-0">
                                                <label class="form-label label-precio" for="precio-0">Precio <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center precio-0 precio @error('detalle.0.precio') is-invalid @enderror" id="precio-0" name="detalles[0][precio]" value="{{old('detalles.0.precio')}}" data-id="0">
                                                @error('detalles.0.precio')
                                                    <span class="invalidad-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="articulo-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 d-flex justify-content-end mb-5">
                            <div class="col-lg-2 text-center">
                                <label class="form-label" for="total">Total</label>
                                <input type="text" class="form-control text-center total" id="total" name="total" value="{{old('total', 0)}}" readonly>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js')}}"></script>
        @include('ventas.scripts.create-scripts')
        @include('ventas.scripts.create-detalles-scripts')
    @endsection
@endcan
