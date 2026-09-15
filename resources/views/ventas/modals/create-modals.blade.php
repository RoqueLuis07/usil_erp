<!-- metodoPagoModal -->
<div class="modal fade flip" id="metodoPagoModal" tabindex="-1" aria-labelledby="metodoPagoModal" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="metodoPagoModal">Agregar Método de Pago</h5>
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
                <div class="forma-pago-fila">
                    <div class="row">
                        <div class="col-lg-10 mb-3">
                            <label class="form-label" for="metodo_pago_dos">Método de Pago <span class="text-danger">(*)</span></label>
                            <select class="form-control selectpicker forma_pago @error('metodo_pago_dos') is-invalid @enderror" id="metodo_pago_dos" name="metodo_pago_dos" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($formas_pagos_dos as $forma)
                                    <option value="{{$forma->id}}" @if (old('metodo_pago_dos') == strval($forma->id)) selected @endif>{{$forma->nombre}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-pagos-0">

                            </span>
                        </div>
                        <hr>
                        <div id="metodo-pago-campos">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-modal-btn">Cerrar</button>
                <button type="button" class="btn btn-success" id="facturar-btn">Facturar</button>
            </div>
        </div>
    </div>
</div>
<!-- /metodoPagoModal -->