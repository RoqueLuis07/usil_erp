@foreach ($pagos as $pago)
    <!-- showModal -->
    <div class="modal fade flip" id="showModal-{{$pago->id}}" tabindex="-1" aria-labelledby="showModal-{{$pago->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModal-{{$pago->id}}">Ver Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="fecha">Fecha</label>
                            <input type="text" class="form-control" id="fecha" @if ($pago->fecha) value="{{Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i:s')}}" @else value="---" @endif readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="orden_pago">OP N°</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="orden_pago" value="{{$pago->orden_pago_id}}" readonly>
                                @can('ver_ordenes_pagos')
                                    <a type="button" class="btn btn-primary" href="{{route('ordenes_pagos.show', $pago->orden_pago_id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Venta"><i class="ri-eye-fill align-bottom"></i></a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="forma_pago">Forma de Pago</label>
                            <input type="text" class="form-control" id="forma_pago" value="{{$pago->formaPago->nombre}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="monto">Monto</label>
                            <input type="text" class="form-control" id="monto" value="{{number_format($pago->monto, 0, ',', '.')}}" readonly>
                        </div>
                    </div>
                    @if ($pago->proveedor_id)
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="proveedor">Proveedor</label>
                                <input type="text" class="form-control" id="proveedor" value="{{ $pago->proveedor->razon_social }}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="ruc">RUC</label>
                                <input type="text" class="form-control" id="ruc" value="{{ $pago->proveedor->ruc }}" readonly>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="estado">Estado</label>
                            <input type="text" class="form-control" id="estado" @if ($pago->estado == 'PE') value="PENDIENTE" @elseif ($pago->estado == 'PA') value="PAGADO" @elseif ($pago->estado == 'AN') value="ANULADO" @endif readonly>
                        </div>
                    </div>
                    <hr>
                    @if ($pago->forma_pago_id == 1)
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label class="form-label" for="caja">Caja</label>
                                <input type="text" class="form-control" id="caja" value="{{$pago->caja->nombre}}" readonly>
                            </div>
                        </div>
                    @elseif ($pago->forma_pago_id == 7)
                        <div class="row">
                            @if ($pago->cuenta_bancaria_id)
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria</label>
                                    <input type="text" class="form-control" id="cuenta_bancaria" value="{{$pago->cuentaBancaria->banco->nombre}}" readonly>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="numero_cheque">N° de Cheque</label>
                                <input type="text" class="form-control" id="numero_cheque" value="{{$pago->numero_cheque}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="numero_serie_cheque">N° de Serie</label>
                                <input type="text" class="form-control" id="numero_serie_cheque" value="{{$pago->numero_serie_cheque}}" readonly>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            @if ($pago->cuenta_bancaria_id)
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria</label>
                                    <input type="text" class="form-control" id="cuenta_bancaria" value="{{$pago->cuentaBancaria->banco->nombre}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="numero_cuenta_bancaria">N° de Cuenta</label>
                                    <input type="text" class="form-control" id="numero_cuenta_bancaria" value="{{$pago->cuentaBancaria->numero_cuenta}}" readonly>
                                </div>
                            @endif
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

    <!-- storeModal -->
        @can('crear_pagos')
            <div class="modal fade flip" id="storeModal-{{$pago->id}}" tabindex="-1" aria-labelledby="storeModal-{{$pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="store-form-{{$pago->id}}" action="{{route('pagos.store', $pago->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de realizar el pago de la OP N° {{$pago->orden_pago_id}} del proveedor {{$pago->proveedor->razon_social}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, realizar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /storeModal -->

    <!-- unactivateModal -->
        @can('anular_pagos')
            <div class="modal fade flip" id="unactivateModal-{{$pago->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$pago->id}}" action="{{route('pagos.unactivate', $pago->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular el pago de la OP N° {{$pago->orden_pago_id}} del proveedor {{$pago->proveedor->razon_social}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, anular!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /unactivateModal -->
@endforeach
