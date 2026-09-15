@can('ver_recibos')
    @extends('layouts.master')
    @section('title') Ver Recibo @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Recibos @endslot
            @slot('title') Ver Recibo  @endslot
        @endcomponent

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-12">
                                <h4 class="card-title mb-0">Visualizar recibo</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{$recibo->fecha}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_factura">N° Recibo</label>
                                    <input type="text" class="form-control text-center" id="numero_factura" value="{{$recibo->numero}}" readonly>
                                </div>
                                <div class="col-lg-8 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="estado">Estado</label>
                                        <input type="text" class="form-control text-center" id="estado" @if($recibo->estado == 'AC') value="FACTURADO" @else value="ANULADO" @endif readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cliente">Cliente</label>
                                    <input type="text" class="form-control" id="cliente" value="{{$recibo->cliente->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$recibo->cliente->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-8 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="monto_total">Monto Total</label>
                                            <input type="text" class="form-control text-center" id="monto_total" value="{{ number_format($recibo->monto_total, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">  
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="forma_pago">Forma de Pago</label>
                                    <input type="text" class="form-control text-center" id="forma_pago" value="{{$recibo->formaPago->nombre}}" readonly>
                                </div>
                                @if ($recibo->forma_pago_id == 2)
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="banco_debito">Banco <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="banco_debito">
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="numero_transaccion_debito">N° de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="numero_transaccion_debito">
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_transaccion_debito">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="fecha_transaccion_debito">
                                    </div>
                                @elseif ($recibo->forma_pago_id == 2)
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="banco_credito">Banco <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="banco_credito" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="numero_transaccion_credito">N° de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="numero_transaccion_credito" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_transaccion_credito">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="fecha_transaccion_credito" readonly>
                                    </div>
                                @elseif ($recibo->forma_pago_id == 4)
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="banco_transferencia">Banco Origen <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="banco_transferencia" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="cuenta_bancaria_transferencia">Cuenta Destino <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="cuenta_bancaria_transferencia" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="numero_transaccion_transferencia">N° de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="numero_transaccion_transferencia" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_transaccion_transferencia">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="fecha_transaccion_transferencia" readonly>
                                    </div>
                                @elseif ($recibo->forma_pago_id == 5)
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="cuenta_bancaria_deposito">Cuenta Destino <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="cuenta_bancaria_deposito" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="numero_transaccion_deposito">N° de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="numero_transaccion_deposito" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_transaccion_deposito">Fecha de Transacción <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" id="fecha_transaccion_deposito" readonly>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles del Recibo</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($recibo->detalles as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-3 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="venta">Factura de Venta</label> @endif
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-center" id="venta" value="{{$detalle->venta->numero_factura}}" readonly>
                                                        @can('ver_ventas')
                                                            <a type="button" class="btn btn-primary" href="{{route('ventas.show', $detalle->venta_id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Venta"><i class="ri-eye-fill align-bottom"></i></a>
                                                        @endcan
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="monto">Monto</label> @endif
                                                    <input type="text" class="form-control text-center" id="monto" value="{{number_format($detalle->monto, 0, ',', '.')}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$recibo->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($recibo->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($recibo->anulado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="anulado_por">Anulado por:</label>
                                <br>
                                {{$recibo->anuladoPor->name}}, en fecha: {{\Carbon\Carbon::parse($recibo->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('recibos.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
