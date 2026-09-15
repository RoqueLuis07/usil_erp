@can('ver_cobros')
    @foreach ($venta->cobros as $cobro)
    <!-- showCobroModal -->
        <div class="modal fade flip" id="showCobroModal-{{$cobro->id}}" tabindex="-1" aria-labelledby="showCobroModal-{{$cobro->id}}" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="showCobroModal-{{$cobro->id}}">Ver Detalle del Cobro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="fecha">Fecha</label>
                                <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($cobro->fecha)->format('d/m/Y H:i')}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="monto_total_detalle">Monto</label>
                                <input type="text" class="form-control text-center" id="monto_total_detalle" value="{{number_format($cobro->monto, 0, ',', '.')}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="forma_pago">Forma de Pago</label>
                                <input type="text" class="form-control text-center" id="forma_pago" value="{{$cobro->formaPago->nombre}}" readonly>
                            </div>
                            @if ($cobro->forma_pago_id == 6)
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="nota_credito">N° Nota de Crédito</label>
                                    <input type="text" class="form-control text-center" id="nota_credito" value="{{$cobro->notaCredito->numero_nota_credito}}" readonly>
                                </div>
                            @else
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="text" class="form-control text-center" id="caja" value="{{$cobro->caja->nombre}}" readonly>
                                </div>
                            @endif
                        </div>
                        @if ($cobro->forma_pago_id == 6)
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="text" class="form-control text-center" id="caja" value="{{$cobro->caja->nombre}}" readonly>
                                </div>
                            </div>
                        @endif
                        @if ($cobro->banco_id || $cobro->cuenta_bancaria_id)
                            <div class="row">
                                <div class="@if ($cobro->cuenta_bancaria_id) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="banco_origen">Banco Origen</label>
                                    <input type="text" class="form-control text-center" id="banco_origen" @if ($cobro->banco_id) value="{{$cobro->banco->nombre}}" @else value="-----" @endif readonly>
                                </div>
                                <div class="@if ($cobro->banco_id) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria Destino</label>
                                    <input type="text" class="form-control text-center" id="cuenta_bancaria" @if ($cobro->cuenta_bancaria_id) value="{{$cobro->cuentaBancaria->banco->nombre}} - {{$cobro->cuentaBancaria->numero_cuenta}}" @else value="-----" @endif readonly>
                                </div>
                            </div>
                        @endif
                        @if ($cobro->numero_transaccion || $cobro->fecha_transaccion)
                            <div class="row">
                                <div class="@if ($cobro->fecha_transaccion) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="numero_transaccion">N° de Transacción</label>
                                    <input type="text" class="form-control text-center" id="numero_transaccion" @if ($cobro->forma_pago_id != 1) value="{{$cobro->numero_transaccion}}" @else value="-----" @endif readonly>
                                </div>
                                <div class="@if ($cobro->numero_transaccion) col-lg-6 @else col-lg-12 @endif mb-3 text-center">
                                    <label class="form-label" for="fecha_transaccion">Fecha de Transacción</label>
                                    <input type="text" class="form-control text-center" id="fecha_transaccion" @if ($cobro->forma_pago_id != 1) value="{{Carbon\Carbon::parse($cobro->fecha_transaccion)->format('d/m/Y')}}" @else value="-----" @endif readonly>
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
@endcan
