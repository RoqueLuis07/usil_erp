<!-- createPaisModal -->
    @can('crear_paises')
        <div class="modal fade flip" id="createPaisModal" tabindex="-1" aria-labelledby="createPaisModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-pais-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createPaisModal">Agregar País</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre')}}">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-success" id="save-pais-btn">Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
<!-- /createPaisModal -->

<!-- createCiudadModal -->
    @can('crear_ciudades')
        <div class="modal fade flip" id="createCiudadModal" tabindex="-1" aria-labelledby="createCiudadModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-ciudad-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createCiudadModal">Agregar Ciudad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" value="{{old('nombre')}}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-success" id="save-ciudad-btn">Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
<!-- /createCiudadModal -->
