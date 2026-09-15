@foreach ($extensiones as $extension)
    <!-- subirAdjuntoModal -->
        <div class="modal fade flip" id="subirAdjuntoModal-{{$extension->id}}" tabindex="-1" aria-labelledby="subirAdjuntoModal-{{$extension->id}}" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Subir un archivo</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('pantallas_alumnos.adjuntar_certificado_extensiones_universitarias', $extension->id)}}" method="post" id="store-adjunto-form-{{$extension->id}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mt-3" id="div-adjunto">
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('adjunto') is-invalid @enderror adjunto" id="adjunto-{{$extension->id}}" name="adjunto" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);" data-id="{{$extension->id}}">
                                        <button type="button" class="btn btn-outline-danger eliminar-adjunto" id="eliminar-adjunto-{{$extension->id}}" data-id="{{$extension->id}}" disabled><i class="ri-delete-bin-fill align-bottom me-2" data-id="{{$extension->id}}"></i>Eliminar Archivo</button>
                                        @error('adjunto')
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
                                <label class="form-label" for="vista-imagen-{{$extension->id}}">Visualización Previa</label>
                                <div class="text-center">
                                    <img src="{{asset('storage/no_image.png')}}" alt="Imagen" id="vista-imagen-{{$extension->id}}" style="width: 200px; height:200px;">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success save-adjunto-btn" data-id="{{$extension->id}}">Subir</button>
                                    <button type="button" class="btn btn-danger cancel-adjunto-btn" data-id="{{$extension->id}}">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <!-- /subirAdjuntoModal -->
@endforeach