<!-- createInstitucionEducativaModal -->
    @can('crear_instituciones_educativas')
        <div class="modal fade flip" id="createInstitucionEducativaModal" tabindex="-1" aria-labelledby="createInstitucionEducativaModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5>Agregar Institución Educativa</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                        </div>
                        <form id="store-institucion-educativa-form">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre_institucion_educativa">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" id="nombre_institucion_educativa" name="nombre_institucion_educativa" value="{{old('nombre_institucion_educativa')}}">
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="tipo_institucion_educativa">Tipo</label>
                                    <div class="text-center" id="div-institucion-educativa">
                                        <div class="btn-group" role="group" id="tipo_institucion_educativa">
                                            <input type="radio" class="btn-check" id="tipo_institucion_educativa1" name="tipo_institucion_educativa" value="CO" @if (old('tipo_institucion_educativa') == 'CO') checked @endif>
                                            <label class="btn btn-outline-info mb-0" for="tipo_institucion_educativa1">COLEGIO</label>
                                            <input type="radio" class="btn-check" id="tipo_institucion_educativa2" name="tipo_institucion_educativa" value="UN" @if (old('tipo_institucion_educativa') == 'UN') checked @endif>
                                            <label class="btn btn-outline-info mb-0" for="tipo_institucion_educativa2">UNIVERSIDAD</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-institucion-educativa-btn">Guardar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /createInstitucionEducativaModal -->

