<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Punto de Impresión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="nombre-show">Nombre</label>
                        <input type="text" class="form-control text-center" id="nombre-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="codigo-show">Código</label>
                        <input type="text" class="form-control text-center" id="codigo-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="tipo_documento-show">Tipo de Documento</label>
                        <input type="text" class="form-control text-center" id="tipo_documento-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="timbrado-show">Timbrado</label>
                        <input type="text" class="form-control text-center" id="timbrado-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="numero_desde-show">N° Desde</label>
                        <input type="text" class="form-control text-center" id="numero_desde-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="numero_hasta-show">N° Hasta</label>
                        <input type="text" class="form-control text-center" id="numero_hasta-show" readonly>
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
<div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="store-form">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="createModal">Agregar Punto de Impresión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre')}}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="codigo">Código <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{old('codigo')}}">
                            @error('codigo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="tipo_documento">Tipo de Documento <span class="text-danger">(*)</span></label>
                            <select class="form-control selectpicker @error('tipo_documento') is-invalid @enderror" id="tipo_documento" name="tipo_documento" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($tipos_documentos as $tipo)
                                    <option value="{{$tipo->id}}" @if (old('tipo_documento') == $tipo->id) selected @endif>{{$tipo->nombre}}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="timbrado">Timbrado <span class="text-danger">(*)</span></label>
                            <select class="form-control selectpicker @error('timbrado') is-invalid @enderror" id="timbrado" name="timbrado" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($timbrados as $timbrado)
                                    <option value="{{$timbrado->id}}" @if (old('timbrado') == $timbrado->id) selected @endif>{{$timbrado->numero}}</option>
                                @endforeach
                            </select>
                            @error('timbrado')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="numero_desde">N° Desde <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('numero_desde') is-invalid @enderror" id="numero_desde" name="numero_desde" value="{{old('numero_desde')}}">
                            @error('numero_desde')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="numero_hasta">N° Hasta <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('numero_hasta') is-invalid @enderror" id="numero_hasta" name="numero_hasta" value="{{old('numero_hasta')}}">
                            @error('numero_hasta')
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
<!-- /createModal -->

<!-- editModal -->
<div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="uptext-form">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="editModal">Actualizar Punto de Impresión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="nombre-edit">Nombre <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('nombre') is-invalid @enderror" id="nombre-edit" name="nombre" value="{{old('nombre')}}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="codigo-edit">Código <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('codigo') is-invalid @enderror" id="codigo-edit" name="codigo" value="{{old('codigo', Carbon\Carbon::today()->format('Y-m-d'))}}">
                            @error('codigo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="tipo_documento-edit">Tipo de Documento <span class="text-danger">(*)</span></label>
                            <select class="form-control selectpicker @error('tipo_documento') is-invalid @enderror" id="tipo_documento-edit" name="tipo_documento" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>

                            </select>
                            @error('tipo_documento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="timbrado-edit">Tipo de Documento <span class="text-danger">(*)</span></label>
                            <select class="form-control selectpicker @error('timbrado') is-invalid @enderror" id="timbrado-edit" name="timbrado" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>

                            </select>
                            @error('timbrado')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="numero_desde-edit">N° Desde <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('numero_desde') is-invalid @enderror" id="numero_desde-edit" name="numero_desde" value="{{old('numero_desde')}}">
                            @error('numero_desde')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="numero_hasta-edit">N° Hasta <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control text-center @error('numero_hasta') is-invalid @enderror" id="numero_hasta-edit" name="numero_hasta" value="{{old('numero_hasta', Carbon\Carbon::today()->format('Y-m-d'))}}">
                            @error('numero_hasta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-btn">Cerrar</button>
                    <button type="button" class="btn btn-success uptext-btn">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /editModal -->

<!-- unactivateModal -->
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
<!-- /unactivateModal -->

<!-- activateModal -->
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
<!-- /activateModal -->

<!-- destroyModal -->
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
<!-- /destroyModal -->
