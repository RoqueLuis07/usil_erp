<!-- cobrarModal -->
<div class="modal fade flip" id="cobrarModal" tabindex="-1" aria-labelledby="cobrarModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="cobrarModal">Cobrar Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="input-imprimir" name="input-imprimir" value="NO">
                    <div class="col-lg-6">
                        <p class="text-muted">
                            <span class="fw-bold">Total a Pagar:</span>
                            <span id="total_pagar_pago"></span>
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <p class="text-danger" style="text-align: right!important;">
                            <span class="fw-bold">Pendiente:</span>
                            <span id="saldo_pago"></span>
                        </p>
                    </div>
                </div>
                <div class="forma-pago-fila forma-pago-fila-0" id="forma-pago-fila-0">
                    <div class="row">
                        <div class="col-lg-10 mb-3">
                            <label class="form-label" for="forma-pago-0">Forma de Pago <span class="text-danger">(*)</span></label>
                            <select class="form-control selectpicker forma_pago @error('pagos.0.forma_pago') is-invalid @enderror" id="forma_pago-0" name="pagos[0][forma_pago]" data-live-search="true" data-id="0">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($formas_pagos as $forma)
                                    <option value="{{$forma->id}}" @if (old('pagos.0.forma_pago') == strval($forma->id)) selected @endif>{{$forma->nombre}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-pagos-0">

                            </span>
                        </div>
                        <div class="col-lg-2 mb-3 text-center div-btn-add-pago-0" style="margin-top: 30px;">
                            <button type="button" class="btn btn-icon btn-success btn-add-pago" id="btn-add-pago-0" data-id="0"><i class="ri-add-fill"></i></button>
                        </div>
                        <hr>
                        <div id="forma-pago-campos-0">

                        </div>
                    </div>
                </div>
                <div id="forma-pago-fila">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-btn">Cerrar</button>
                <button type="button" class="btn btn-success" id="facturar-btn" disabled>Facturar</button>
            </div>
        </div>
    </div>
</div>
<!-- /cobrarModal -->
