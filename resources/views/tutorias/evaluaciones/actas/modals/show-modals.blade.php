<!-- subirActaModal -->
    @can('subir_adjunto_actas_tutorias')
        <div class="modal fade flip" id="subirActaModal" tabindex="-1" aria-labelledby="subirActaModal" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Subir un archivo</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('tutorias_evaluaciones.subir_acta', $tutoria->id)}}" method="post" id="store-acta-form" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mt-3" id="div-acta">
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('acta') is-invalid @enderror" id="acta" name="acta" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-acta" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('acta')
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
                                    <button type="button" class="btn btn-success" id="save-acta-btn" data-id="{{$tutoria->id}}" data-url="{{route('tutorias_evaluaciones.subir_acta', $tutoria->id)}}">Subir</button>
                                    <button type="button" class="btn btn-danger" id="cancel-acta-btn">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /subirActaModal -->
