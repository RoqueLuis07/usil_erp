<!-- showModal -->
<div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="showModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showModal">Ver Fecha de Desmatriculación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="semestre-show">Semestre</label>
                        <input type="text" class="form-control" id="semestre-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="programa-show">Programa</label>
                        <input type="text" class="form-control" id="programa-show" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="fecha_inicio-show">Fecha de Inicio</label>
                        <input type="text" class="form-control text-center" id="fecha_inicio-show" readonly>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="fecha_fin-show">Fecha de Fin</label>
                        <input type="text" class="form-control text-center" id="fecha_fin-show" readonly>
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
    @can('crear_fechas_desmatriculaciones')
        <div class="modal fade flip" id="createModal" tabindex="-1" aria-labelledby="createModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="store-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="createModal">Agregar Fecha de Desmatriculación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="semestre">Semestre <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('semestre') is-invalid @enderror" id="semestre" name="semestre" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($semestres as $semestre)
                                            <option value="{{ $semestre->id }}" @if (old('semestre') == strval($semestre->id)) selected @endif>{{$semestre->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('semestre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="programa">Programa <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('programa') is-invalid @enderror" id="programa" name="programa" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{ $programa->id }}" @if (old('programa') == strval($programa->id)) selected @endif>{{$programa->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('programa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha de Inicio <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio', Carbon\Carbon::now()->format('Y-m-d'))}}">
                                    @error('fecha_inicio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha de Fin <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin', Carbon\Carbon::now()->addMonth()->format('Y-m-d'))}}">
                                    @error('fecha_fin')
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
    @can('editar_fechas_desmatriculaciones')
        <div class="modal fade flip" id="editModal" tabindex="-1" aria-labelledby="editModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="update-form">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editModal">Actualizar Fecha de Desmatriculación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="semestre-edit">Semestre <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('semestre') is-invalid @enderror" id="semestre-edit" name="semestre" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($semestres as $semestre)
                                            <option value="{{ $semestre->id }}" @if (old('semestre') == strval($semestre->id)) selected @endif>{{$semestre->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('semestre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div><div class="col-lg-6 mb-3">
                                    <label class="form-label" for="programa-edit">Programa <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('programa') is-invalid @enderror" id="programa-edit" name="programa" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{ $programa->id }}" @if (old('programa') == strval($programa->id)) selected @endif>{{$programa->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('programa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha_inicio-edit">Fecha de Inicio <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio-edit" name="fecha_inicio" value="{{old('fecha_inicio')}}">
                                    @error('fecha_inicio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha_fin-edit">Fecha de Fin <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control text-center @error('fecha_fin') is-invalid @enderror" id="fecha_fin-edit" name="fecha_fin" value="{{old('fecha_fin')}}">
                                    @error('fecha_fin')
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
    @can('inactivar_fechas_desmatriculaciones')
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
    @can('activar_fechas_desmatriculaciones')
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
    @can('eliminar_fechas_desmatriculaciones')
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
