<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Precio de Tutoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 mb-3 text-center">
                        <label class="form-label" for="modalidad-show">Modalidad</label>
                        <input type="text" class="form-control text-center" id="modalidad-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3 text-center">
                        <label class="form-label" for="carrera-show">Carrera</label>
                        <input type="text" class="form-control text-center" id="carrera-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 mb-3 text-center">
                        <label class="form-label" for="articulo-show">Artículo</label>
                        <input type="text" class="form-control text-center" id="articulo-show" readonly>
                    </div>
                    <div class="col-lg-4 mb-3 text-center">
                        <label class="form-label" for="precio-show">Precio</label>
                        <input type="text" class="form-control text-center" id="precio-show" readonly>
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
    @can('editar_precios_tutorias')
        <div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createModal">Agregar Precio de Tutoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="modalidad">Modalidad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($modalidades as $modalidad)
                                            <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('modalidad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="carrera">Carrera <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($carreras as $carrera)
                                            <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id)) selected @endif data-subtext="{{ $carrera->programa->nombre }}">{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('carrera')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8 mb-3">
                                    <label class="form-label" for="articulo">Artículo <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('articulo') is-invalid @enderror" id="articulo" name="articulo" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($articulos as $articulo)
                                            <option value="{{$articulo->id}}" @if (old('articulo') == strval($articulo->id)) selected @endif>{{$articulo->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('articulo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="precio">Precio</label>
                                    <input type="text" class="form-control text-center @error('precio') is-invalid @enderror" id="precio" name="precio" value="{{old('precio')}}" readonly>
                                    @error('precio')
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
    @can('editar_precios_tutorias')
        <div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="update-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editModal">Actualizar Precio de Tutoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="modalidad-edit">Modalidad <span class="text-danger">(*)</span></label>
                                    <select class="form-control @error('modalidad') is-invalid @enderror" id="modalidad-edit" name="modalidad" data-live-search="true">

                                    </select>
                                    @error('modalidad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="carrera-edit">Carrera <span class="text-danger">(*)</span></label>
                                    <select class="form-control @error('carrera') is-invalid @enderror" id="carrera-edit" name="carrera" data-live-search="true">

                                    </select>
                                    @error('carrera')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8 mb-3">
                                    <label class="form-label" for="articulo-edit">Artículo <span class="text-danger">(*)</span></label>
                                    <select class="form-control @error('articulo') is-invalid @enderror" id="articulo-edit" name="articulo" data-live-search="true">

                                    </select>
                                    @error('articulo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="precio-edit">Precio</label>
                                    <input type="text" class="form-control text-center @error('precio') is-invalid @enderror" id="precio-edit" name="precio" value="{{old('precio')}}" readonly>
                                    @error('precio')
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

<!-- destroyModal -->
    @can('eliminar_precios_tutorias')
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
