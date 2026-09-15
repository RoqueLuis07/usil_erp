<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Institución Educativa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label class="form-label" for="nombre-show">Nombre</label>
                        <input type="text" class="form-control" id="nombre-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <label class="form-label" for="tipo-show">Tipo</label>
                        <input type="text" class="form-control" id="tipo-show" readonly>
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
    @can('crear_instituciones_educativas')
        <div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createModal">Agregar Institución Educativa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre-institucion-educativa">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_institucion_educativa') is-invalid @enderror" id="nombre-institucion-educativa" name="nombre_institucion_educativa" value="{{old('nombre_institucion_educativa')}}">
                                    @error('nombre_institucion_educativa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tipo-institucion-educativa">Tipo <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control" id="tipo-institucion-educativa" name="tipo_institucion_educativa">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="CO">COLEGIO</option>
                                        <option value="UN">UNIVERSIDAD</option>
                                    </select>
                                    @error('tipo_institucion_educativa')
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
    @can('editar_instituciones_educativas')
        <div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="update-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editModal">Actualizar Institución Educativa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre-institucion-educativa-edit">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_institucion_educativa') is-invalid @enderror" id="nombre-institucion-educativa-edit" name="nombre_institucion_educativa" value="{{old('nombre_institucion_educativa')}}">
                                    @error('nombre_institucion_educativa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tipo-institucion-educativa-edit">Tipo <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control" id="tipo-institucion-educativa-edit" name="tipo_institucion_educativa">

                                    </select>
                                    @error('tipo_institucion_educativa')
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
    @can('inactivar_instituciones_educativas')
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
    @can('activar_instituciones_educativas')
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
    @can('eliminar_instituciones_educativas')
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
