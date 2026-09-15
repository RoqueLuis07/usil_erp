<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Timbrado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label class="form-label" for="numero-show">N° de Timbrado</label>
                        <input type="text" class="form-control" id="numero-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="valido_desde-show">Válido Desde</label>
                        <input type="text" class="form-control text-center" id="valido_desde-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="valido_hasta-show">Válido Hasta</label>
                        <input type="text" class="form-control text-center" id="valido_hasta-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label class="form-label" for="estado-show">Estado</label>
                        <input type="text" class="form-control" id="estado-show" readonly>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="col-lg-5 mb-3 text-center" id="cargado">
                        <label class="form-label" for="cargado_por">Cargado por:</label>
                        <br>
                        <textarea class="form-control" id="span-cargado" cols="30" rows="3" readonly></textarea>
                    </div>
                    <div class="col-lg-6 mb-3 text-center" id="actualizado" hidden>
                        <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                        <br>
                        <textarea class="form-control" id="span-actualizado" cols="30" rows="3" readonly></textarea>
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
    @can('crear_timbrados')
        <div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createModal">Agregar Timbrado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="numero">N° de Timbrado <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control numero @error('numero') is-invalid @enderror" id="numero" name="numero" value="{{old('numero')}}">
                                    @error('numero')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="valido_desde">Válido Desde <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('valido_desde') is-invalid @enderror" id="valido_desde" name="valido_desde" value="{{old('valido_desde', Carbon\Carbon::today()->format('Y-m-d'))}}">
                                    @error('valido_desde')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="valido_hasta">Válido Hasta <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('valido_hasta') is-invalid @enderror" id="valido_hasta" name="valido_hasta" value="{{old('valido_hasta', Carbon\Carbon::today()->addMonth()->format('Y-m-d'))}}">
                                    @error('valido_hasta')
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
    @can('editar_timbrados')
        <div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="update-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editModal">Actualizar Timbrado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="numero-edit">N° de Timbrado <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control numero @error('numero') is-invalid @enderror" id="numero-edit" name="numero" value="{{old('numero')}}">
                                    @error('numero')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="valido_desde-edit">Válido Desde <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('valido_desde') is-invalid @enderror" id="valido_desde-edit" name="valido_desde" value="{{old('valido_desde')}}">
                                    @error('valido_desde')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                    @error('valido_desde')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="valido_hasta-edit">Válido Hasta <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('valido_hasta') is-invalid @enderror" id="valido_hasta-edit" name="valido_hasta" value="{{old('valido_hasta')}}">
                                    @error('valido_hasta')
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
    @can('inactivar_timbrados')
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
    @can('activar_timbrados')
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
    @can('eliminar_timbrados')
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
