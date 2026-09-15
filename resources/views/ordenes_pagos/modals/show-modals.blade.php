@can('ver_pagos')
    <!-- showPagoModal -->
        <div class="modal fade flip" id="showPagoModal-{{$orden_pago->pago->id}}" tabindex="-1" aria-labelledby="showPagoModal-{{$orden_pago->pago->id}}" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="showPagoModal-{{$orden_pago->pago->id}}">Ver Detalle del Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="fecha">Fecha</label>
                                <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($orden_pago->pago->fecha)->format('d/m/Y H:i')}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="monto_total_detalle">Monto</label>
                                @php
                                    if ($orden_pago->moneda_id == 1) {
                                        $decimales = 0;
                                    } else {
                                        $decimales = 2;
                                    }
                                @endphp
                                <input type="text" class="form-control text-center" id="monto_total_detalle" value="{{number_format($orden_pago->pago->monto, $decimales, ',', '.')}}  {{$orden_pago->moneda->codigo}}">
                            </div>
                        </div>
                        @if ($orden_pago->pago->caja_id == 6)
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="text" class="form-control text-center" id="caja" value="{{$orden_pago->pago->caja->nombre}}" readonly>
                                </div>
                            </div>
                        @endif
                        @if ($orden_pago->pago->cuenta_bancaria_id)
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria</label>
                                    <input type="text" class="form-control text-center" id="cuenta_bancaria" value="{{$orden_pago->pago->cuentaBancaria->banco->nombre}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="numero_cuenta_bancaria">N° de Cuenta</label>
                                    <input type="text" class="form-control text-center" id="numero_cuenta_bancaria" value="{{$orden_pago->pago->cuentaBancaria->numero_cuenta}}" readonly>
                                </div>
                            </div>
                        @endif
                        @if ($orden_pago->forma_pago_id == 7)
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="numero_cheque">N° de Cheque</label>
                                    <input type="text" class="form-control text-center" id="numero_cheque" value="{{$orden_pago->pago->numero_cheque}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="numero_serie_cheque">N° de Serie</label>
                                    <input type="text" class="form-control text-center" id="numero_serie_cheque" value="{{$orden_pago->pago->numero_serie_cheque}}" readonly>
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
    <!-- /showPagoModal -->
@endcan
