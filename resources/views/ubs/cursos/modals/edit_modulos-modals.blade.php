<!-- createModuloModal -->
    @can('crear_modulos_ubs')
        <div class="modal fade flip" id="createModuloModal" tabindex="-1" aria-labelledby="createModuloModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5>Agregar Módulo</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                        </div>
                        <form id="store-modulo-form">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="nombre_fantasia">Nombre Fantasía <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" id="nombre_fantasia" name="nombre_fantasia" value="{{old('nombre_fantasia')}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="nombre_real">Nombre Real <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" id="nombre_real" name="nombre_real" value="{{old('nombre_real')}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="codigo">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" value="{{old('codigo')}}">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="carga_horaria">Carga Horaria <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" id="carga_horaria" name="carga_horaria" value="{{old('carga_horaria')}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-modulo-btn">Guardar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /createModuloModal -->

