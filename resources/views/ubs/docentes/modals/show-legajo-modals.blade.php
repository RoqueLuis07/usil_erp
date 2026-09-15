<!-- subirLegajoModal -->
    @can('subir_legajos_docentes_ubs')
        <div class="modal fade flip" id="subirLegajoModal" tabindex="-1" aria-labelledby="subirLegajoModal" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Subir un archivo</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('docentes_ubs.subir_legajo', $docente->id)}}" method="post" id="store-docente-legajo-form" enctype="multipart/form-data">
                            @csrf
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito.</p>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="tipo_legajo">Tipo de Legajo <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_legajo') is-invalid @enderror" id="tipo_legajo" name="tipo_legajo" data-live-search="false">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_legajos as $tipo)
                                            <option value="{{$tipo->id}}" @if (old('tipo_legajo') == strval($tipo->id)) selected @endif>{{$tipo->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_legajo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="titulo_obtenido">Título Obtenido <span class="text-danger" id="span-titulo-obtenido"></span></label>
                                    <input type="text" class="form-control @error('titulo_obtenido') is-invalid @enderror" id="titulo_obtenido" name="titulo_obtenido" value="{{old('titulo_obtenido')}}" placeholder="Escriba el título obtenido" disabled>
                                    @error('titulo_obtenido')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="institucion_educativa">Institución Educativa <span class="text-danger" id="span-institucion-educativa"></span></label>
                                    <select class="form-control @error('institucion_educativa') is-invalid @enderror" id="institucion_educativa" name="institucion_educativa" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($instituciones_educativas as $institucion_educativa)
                                            <option value="{{$institucion_educativa->id}}" @if (old('institucion_educativa') == strval($institucion_educativa->id)) selected @endif>{{$institucion_educativa->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('institucion_educativa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="pais">País <span class="text-danger" id="span-pais"></span></label>
                                    <select class="form-control @error('pais') is-invalid @enderror" id="pais" name="pais" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($paises as $pais)
                                            <option value="{{$pais->id}}" @if (old('pais') == strval($pais->id)) selected @endif>{{$pais->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('pais')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mt-3" id="div-legajo">
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('legajo') is-invalid @enderror" id="legajo" name="legajo" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-legajo" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('legajo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <p class="text-muted">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code>, <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <label class="form-label" for="vista-imagen">Visualización Previa</label>
                                <div class="text-center">
                                    <img src="{{asset('storage/no_image.png')}}" alt="Imagen" id="vista-imagen" style="width: 200px; height:200px;">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-legajo-btn" data-id="{{$docente->id}}">Subir</button>
                                    <button type="button" class="btn btn-danger" id="cancel-legajo-btn">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /subirLegajoModal -->

