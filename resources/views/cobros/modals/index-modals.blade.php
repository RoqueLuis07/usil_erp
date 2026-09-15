@foreach ($cobros as $cobro)
    <!-- showModal -->
    <div class="modal fade flip" id="showModal-{{$cobro->id}}" tabindex="-1" aria-labelledby="showModal-{{$cobro->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModal-{{$cobro->id}}">Ver Cobro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="fecha">Fecha</label>
                            <input type="text" class="form-control" id="fecha" value="{{Carbon\Carbon::parse($cobro->created_at)->format('d/m/Y H:i:s')}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="venta">Factura de Venta</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="venta" value="{{$cobro->venta->numero_factura}}" readonly>
                                @can('ver_ventas')
                                    <a type="button" class="btn btn-primary" href="{{route('ventas.show', $cobro->venta_id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Venta"><i class="ri-eye-fill align-bottom"></i></a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="forma_pago">Forma de Pago</label>
                            <input type="text" class="form-control" id="forma_pago" value="{{$cobro->formaPago->nombre}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="monto">Monto</label>
                            <input type="text" class="form-control" id="monto" value="{{number_format($cobro->monto, 0, ',', '.')}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="estado">Estado</label>
                            <input type="text" class="form-control" id="estado" @if ($cobro->estado == 'AC') value="ACTIVO" @elseif ($cobro->estado == 'IN') value="ANULADO" @endif readonly>
                        </div>
                    </div>
                    <hr>
                    @if ($cobro->forma_pago_id == 1)
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label class="form-label" for="caja">Caja</label>
                                <input type="text" class="form-control" id="caja" value="{{$cobro->caja->nombre}}" readonly>
                            </div>
                        </div>
                    @endif
                    @if ($cobro->forma_pago_id != 1)
                        <div class="row">
                            @if ($cobro->banco_id)
                                <div class="@if($cobro->cuenta_bancaria_id) col-lg-6 @else col-lg-12 @endif mb-3">
                                    <label class="form-label" for="banco">Banco Origen</label>
                                    <input type="text" class="form-control" id="banco" value="{{$cobro->banco->nombre}}" readonly>
                                </div>
                            @endif
                            @if ($cobro->cuenta_bancaria_id)
                                <div class="@if($cobro->banco_id) col-lg-6 @else col-lg-12 @endif mb-3">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Destino</label>
                                    <input type="text" class="form-control" id="cuenta_bancaria" value="{{$cobro->cuentaBancaria->banco->nombre}}" readonly>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="numero_transaccion">Número de Transacción</label>
                                <input type="text" class="form-control" id="numero_transaccion" value="{{$cobro->numero_transaccion}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="fecha_transaccion">Fecha de Transacción</label>
                                <input type="text" class="form-control" id="fecha_transaccion" value="{{Carbon\Carbon::parse($cobro->fecha_transaccion)->format('d/m/Y')}}" readonly>
                            </div>
                        </div>   
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /showModal -->
@endforeach
