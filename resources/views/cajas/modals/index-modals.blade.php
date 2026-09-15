<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="nombre-show">Nombre</label>
                        <input type="text" class="form-control" id="nombre-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="usuario-show">Usuario</label>
                        <input type="text" class="form-control" id="usuario-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="efectivo-show">Efectivo</label>
                        <input type="text" class="form-control" id="efectivo-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="cheque-show">Cheques</label>
                        <input type="text" class="form-control" id="cheque-show" readonly>
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
    @can('crear_cajas')
        <div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createModal">Agregar Caja</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre')}}">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="usuario">Usuario <span class="text-danger">(*)</span></label>
                                        <select class="form-control selectpicker @error('usuario') is-invalid @enderror" id="usuario" name="usuario" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($usuarios as $usuario)
                                                <option value="{{$usuario->id}}" @if (old('usuario') == strval($usuario->id)) selected @endif>{{$usuario->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('usuario')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="efectivo">Efectivo</label>
                                        <input type="text" class="form-control" id="efectivo" value="0" readonly>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="cheque">Cheques</label>
                                        <input type="text" class="form-control" id="cheque" value="0" readonly>
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
    @can('editar_cajas')
        <div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="update-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editModal">Actualizar Caja</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre-edit">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre-edit" name="nombre" value="{{old('nombre')}}">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="usuario-edit">Usuario <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('usuario') is-invalid @enderror" id="usuario-edit" name="usuario" data-live-search="true">

                                    </select>
                                    @error('usuario')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="efectivo-edit">Efectivo</label>
                                    <input type="text" class="form-control" id="efectivo-edit" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="cheque-edit">Cheques</label>
                                    <input type="text" class="form-control" id="cheque-edit" readonly>
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
    @can('inactivar_cajas')
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
    @can('activar_cajas')
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
    @can('eliminar_cajas')
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
