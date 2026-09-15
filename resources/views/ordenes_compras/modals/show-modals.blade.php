{{-- @can('ver_pagos')
    @foreach ($venta->pagos as $pago)
    <!-- showCobroModal -->
        <div class="modal fade flip" id="showCobroModal-{{$pago->id}}" tabindex="-1" aria-labelledby="showCobroModal-{{$pago->id}}" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="showCobroModal-{{$pago->id}}">Ver Detalle del Cobro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="fecha">Fecha</label>
                                <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i')}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="monto_total_detalle">Monto</label>
                                <input type="text" class="form-control text-center" id="monto_total_detalle" value="{{number_format($pago->monto, 0, ',', '.')}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="forma_pago">Forma de Pago</label>
                                <input type="text" class="form-control text-center" id="forma_pago" value="{{$pago->formaPago->nombre}}" readonly>
                            </div>
                            @if ($pago->forma_pago_id == 6)
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="nota_credito">N° Nota de Crédito</label>
                                    <input type="text" class="form-control text-center" id="nota_credito" value="{{$pago->notaCredito->numero_nota_credito}}" readonly>
                                </div>
                            @else
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="text" class="form-control text-center" id="caja" value="{{$pago->caja->nombre}}" readonly>
                                </div>
                            @endif
                        </div>
                        @if ($pago->forma_pago_id == 6)
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="text" class="form-control text-center" id="caja" value="{{$pago->caja->nombre}}" readonly>
                                </div>
                            </div>
                        @endif
                        @if ($pago->banco_id || $pago->cuenta_bancaria_id)
                            <div class="row">
                                <div class="@if ($pago->cuenta_bancaria_id) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="banco_origen">Banco Origen</label>
                                    <input type="text" class="form-control text-center" id="banco_origen" @if ($pago->banco_id) value="{{$pago->banco->nombre}}" @else value="-----" @endif readonly>
                                </div>
                                <div class="@if ($pago->banco_id) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria Destino</label>
                                    <input type="text" class="form-control text-center" id="cuenta_bancaria" @if ($pago->cuenta_bancaria_id) value="{{$pago->cuentaBancaria->banco->nombre}} - {{$pago->cuentaBancaria->numero_cuenta}}" @else value="-----" @endif readonly>
                                </div>
                            </div>
                        @endif
                        @if ($pago->numero_transaccion || $pago->fecha_transaccion)
                            <div class="row">
                                <div class="@if ($pago->fecha_transaccion) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="numero_transaccion">N° de Transacción</label>
                                    <input type="text" class="form-control text-center" id="numero_transaccion" @if ($pago->forma_pago_id != 1) value="{{$pago->numero_transaccion}}" @else value="-----" @endif readonly>
                                </div>
                                <div class="@if ($pago->numero_transaccion) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="fecha_transaccion">Fecha de Transacción</label>
                                    <input type="text" class="form-control text-center" id="fecha_transaccion" @if ($pago->forma_pago_id != 1) value="{{Carbon\Carbon::parse($pago->fecha_transaccion)->format('d/m/Y')}}" @else value="-----" @endif readonly>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /showCobroModal -->
    @endforeach
@endcan --}}
