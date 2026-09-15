<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Cuenta Bancaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="banco-show">Banco</label>
                        <input type="text" class="form-control" id="banco-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="numero_cuenta-show">N° de Cuenta</label>
                        <input type="text" class="form-control" id="numero_cuenta-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="tipo_cuenta-show">Tipo de Cuenta</label>
                        <input type="text" class="form-control" id="tipo_cuenta-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="monto-show">Monto</label>
                        <input type="text" class="form-control" id="monto-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="moneda-show">Moneda</label>
                        <input type="text" class="form-control" id="moneda-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3 text-center">
                        <div>
                            <label class="form-label" for="acredita_tarjeta">Acredita Tarjeta ?</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" id="acredita_tarjeta1-show" disabled>
                            <label class="btn btn-outline-danger" for="acredita_tarjeta1-show">No</label>
                            <input type="radio" class="btn-check" id="acredita_tarjeta2-show" disabled>
                            <label class="btn btn-outline-success" for="acredita_tarjeta2-show">Sí</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="titular-show">Titular</label>
                        <input type="text" class="form-control" id="titular-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="documento_titular-show">N° de Documento</label>
                        <input type="text" class="form-control" id="documento_titular-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="cuenta_ingreso-show">Cta. Contable Ingreso</label>
                        <input type="text" class="form-control" id="cuenta_ingreso-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="cuenta_egreso-show">Cta. Contable Egreso</label>
                        <input type="text" class="form-control" id="cuenta_egreso-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label class="form-label" for="estado-show">Estado</label>
                        <input type="text" class="form-control" id="estado-show" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- /showModal -->

<!-- createModal -->
    @can('crear_cuentas_bancarias')
        <div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createModal">Agregar Cuenta Bancaria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="banco">Banco <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('banco') is-invalid @enderror" id="banco" name="banco" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($bancos as $banco)
                                            <option value="{{$banco->id}}" @if (old('banco') == strval($banco->id)) selected @endif>{{$banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('banco')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="numero_cuenta">N° de Cuenta <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_cuenta') is-invalid @enderror" id="numero_cuenta" name="numero_cuenta" value="{{old('numero_cuenta')}}">
                                    @error('numero_cuenta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tipo_cuenta">Tipo de Cuenta <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('tipo_cuenta') is-invalid @enderror" id="tipo_cuenta" name="tipo_cuenta" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="CA">CAJA DE AHORRO</option>
                                        <option value="CC">CUENTA CORRIENTE</option>
                                    </select>
                                    @error('tipo_cuenta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="monto">Monto</label>
                                    <input type="text" class="form-control" id="monto" value="0" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="moneda">Moneda <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('moneda') is-invalid @enderror" id="moneda" name="moneda" data-live-search="true">
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
                                <div class="col-lg-6 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="acredita_tarjeta">Acredita Tarjeta ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group div-error-acredita_tarjeta @error('acredita_tarjeta') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check" id="acredita_tarjeta1" name="acredita_tarjeta" value="false" @if (old('acredita_tarjeta') == 'false') checked @endif>
                                        <label class="btn btn-outline-danger" for="acredita_tarjeta1">No</label>
                                        <input type="radio" class="btn-check" id="acredita_tarjeta2" name="acredita_tarjeta" value="true" @if (old('acredita_tarjeta') == 'true') checked @endif>
                                        <label class="btn btn-outline-success" for="acredita_tarjeta2">Sí</label>
                                    </div>
                                    <span class="error-acredita_tarjeta">

                                    </span>
                                    @error('acredita_tarjeta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="titular">Titular <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('titular') is-invalid @enderror" id="titular" name="titular" value="{{old('titular')}}">
                                    @error('titular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="documento_titular">N° de Documento <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('documento_titular') is-invalid @enderror" id="documento_titular" name="documento_titular" value="{{old('documento_titular')}}">
                                    @error('documento_titular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="cuenta_ingreso">Cta. Contable Ingreso <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_ingreso') is-invalid @enderror" id="cuenta_ingreso" name="cuenta_ingreso" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_contables as $cuenta_ingreso)
                                            <option value="{{$cuenta_ingreso->id}}" @if (old('cuenta_ingreso') == strval($cuenta_ingreso->id)) selected @endif data-subtext="{{$cuenta_ingreso->cuenta}}">{{$cuenta_ingreso->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_ingreso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="cuenta_egreso">Cta. Contable Egreso <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_egreso') is-invalid @enderror" id="cuenta_egreso" name="cuenta_egreso" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_contables as $cuenta_egreso)
                                            <option value="{{$cuenta_egreso->id}}" @if (old('cuenta_egreso') == strval($cuenta_egreso->id)) selected @endif data-subtext="{{$cuenta_egreso->cuenta}}">{{$cuenta_egreso->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_egreso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-btn">Cerrar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
<!-- /createModal -->

<!-- editModal -->
    @can('editar_cuentas_bancarias')
        <div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="update-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editModal">Actualizar Cuenta Bancaria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="banco-edit">Banco <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('banco') is-invalid @enderror" id="banco-edit" name="banco" data-live-search="true">

                                    </select>
                                    @error('banco')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="numero_cuenta-edit">N° de Cuenta <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_cuenta') is-invalid @enderror" id="numero_cuenta-edit" name="numero_cuenta">
                                    @error('numero_cuenta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tipo_cuenta-edit">Tipo de Cuenta <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('tipo_cuenta') is-invalid @enderror" id="tipo_cuenta-edit" name="tipo_cuenta" data-live-search="true">

                                    </select>
                                    @error('tipo_cuenta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="monto-edit">Monto</label>
                                    <input type="text" class="form-control" id="monto-edit" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="moneda-edit">Moneda <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('moneda') is-invalid @enderror" id="moneda-edit" name="moneda" data-live-search="true">

                                    </select>
                                    @error('moneda')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="acredita_tarjeta">Acredita Tarjeta ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group di-error-acredita_tarjeta @error('acredita_tarjeta') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check" id="acredita_tarjeta1-edit" name="acredita_tarjeta" value="false" @if (old('acredita_tarjeta') == 'false') checked @endif>
                                        <label class="btn btn-outline-danger" for="acredita_tarjeta1-edit">No</label>
                                        <input type="radio" class="btn-check" id="acredita_tarjeta2-edit" name="acredita_tarjeta" value="true" @if (old('acredita_tarjeta') == 'true') checked @endif>
                                        <label class="btn btn-outline-success" for="acredita_tarjeta2-edit">Sí</label>
                                    </div>
                                    <span class="error-acredita_tarjeta">

                                    </span>
                                    @error('acredita_tarjeta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="titular-edit">Titular <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('titular') is-invalid @enderror" id="titular-edit" name="titular">
                                    @error('titular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="documento_titular-edit">N° de Documento <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('documento_titular') is-invalid @enderror" id="documento_titular-edit" name="documento_titular">
                                    @error('documento_titular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="cuenta_ingreso-edit">Cta. Contable Ingreso <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_ingreso') is-invalid @enderror" id="cuenta_ingreso-edit" name="cuenta_ingreso" data-live-search="true">

                                    </select>
                                    @error('cuenta_ingreso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="cuenta_egreso-edit">Cta. Contable Egreso <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_egreso') is-invalid @enderror" id="cuenta_egreso-edit" name="cuenta_egreso" data-live-search="true">

                                    </select>
                                    @error('cuenta_egreso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-btn">Cerrar</button>
                            <button type="button" class="btn btn-success update-btn">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
<!-- /editModal -->

<!-- unactivateModal -->
    @can('incativar_cuentas_bancarias')
        <div class="modal fade flip" id="unactivateModal" tabindex="-1" aria-labelledby="unactivateModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="unactivate-form">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4 class="mensaje"></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger unactivate-btn" data-bs-dismiss="modal">Sí, inactivar!</button>
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

<!-- activateModal -->
    @can('activar_cuentas_bancarias')
        <div class="modal fade flip" id="activateModal" tabindex="-1" aria-labelledby="activateModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="activate-form">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4 class="mensaje"></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success activate-btn" data-bs-dismiss="modal">Sí, activar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /activateModal -->

<!-- destroyModal -->
    @can('eliminar_cuentas_bancarias')
        <div class="modal fade flip" id="destroyModal" tabindex="-1" aria-labelledby="destroyModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="destroy-form">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4 class="mensaje"></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger delete-btn" data-bs-dismiss="modal">Sí, eliminar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /destroyModal -->
