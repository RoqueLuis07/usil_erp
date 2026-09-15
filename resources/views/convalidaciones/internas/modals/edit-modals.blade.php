<!-- certificadoModal -->
    <div class="modal fade flip" id="certificadoModal" tabindex="-1" aria-labelledby="certificadoModal" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">Cambiar Certificado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Al cambiar el <code>certificado de estudios</code>, el adjunto anterior se eliminará.</p>
                    <form id="certificado-form" action="{{route('convalidaciones_externas.change_certificado', $convalidacion->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-8 mb-3">
                                <label class="form-label" for="nombre_alumno">Alumno</label>
                                <input type="text" class="form-control" id="nombre_alumno" value="{{$convalidacion->alumno->primer_nombre}} {{$convalidacion->alumno->primer_apellido}}" readonly>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label class="form-label" for="documento_alumno">N° Documento</label>
                                <input type="text" class="form-control" id="documento_alumno" value="{{$convalidacion->alumno->numero_documento}}" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12 mb-3" id="div-certificado">
                                <label class="form-label" for="certificado">Certificado de Estudios <span class="text-danger">(*)</span></label>
                                <div class="input-group custom-file-button">
                                    <input type="file" class="form-control @error('certificado') is-invalid @enderror" id="certificado" name="certificado" accept="image/jpeg,image/png,application/pdf">
                                    <button type="button" class="btn btn-outline-danger eliminar-certificado" id="eliminar-certificado" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                    @error('certificado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <p class="text-muted" style="font-size: 14px">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code>, <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="hstack gap-2 justify-content-center">
                                <button type="button" class="btn btn-success " id="certificado-btn" data-id="{{$convalidacion->id}}" data-url="{{route('convalidaciones_externas.change_certificado', $convalidacion->id)}}">Cambiar</button>
                                <button type="button" class="btn btn-danger" id="cancel-certificado-btn">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- /certificadoModal -->
