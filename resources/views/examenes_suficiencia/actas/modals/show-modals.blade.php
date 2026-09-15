<!-- subirActaModal -->
    @can('subir_adjunto_actas_examenes_suficiencia')
        <div class="modal fade flip" id="subirActaModal" tabindex="-1" aria-labelledby="subirActaModal" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Subir un archivo</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('examenes_suficiencia.subir_acta', $acta->id)}}" method="post" id="store-acta-form" enctype="multipart/form-data">
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
                                    <button type="button" class="btn btn-success" id="save-acta-btn" data-id="{{$acta->id}}" data-url="{{route('examenes_suficiencia.subir_acta', $acta->id)}}">Subir</button>
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

@foreach ($acta->alumnos as $key => $detalle)
    <!-- editPuntajeModal -->
        @can('editar_puntajes_examanes_suficiencia')
            <div class="modal fade flip" id="editPuntajeModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="editPuntajeModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title">Editar Puntaje</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('examenes_suficiencia.update_puntaje', $detalle->id)}}" method="post" id="update-puntaje-form-{{$detalle->id}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label" for="alumno-{{$detalle->id}}">Alumno</label>
                                        <input type="text" class="form-control" id="alumno-{{$detalle->id}}" value="{{$detalle->alumno->primer_nombre}} {{$detalle->alumno->primer_apellido}}" readonly>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="puntos_obtenidos-{{$detalle->id}}">Puntos Obtenidos <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control text-center puntos_obtenidos puntos_obtenidos-{{$key}} @error('puntos_obtenidos') is-invalid @enderror" id="puntos_obtenidos-{{$detalle->id}}" name="puntos_obtenidos" value="{{$detalle->puntos_examen}}" data-id="{{$detalle->id}}">
                                            @error('puntos_obtenidos')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="calificacion-{{$detalle->id}}">Calififación <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control text-center calificacion-{{$detalle->id}}" id="calificacion-{{$detalle->id}}" name="calificacion" value="{{$detalle->calificacion}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success edit-puntaje-btn" data-id="{{$detalle->id}}" data-acta="{{$acta->id}}" data-url="{{route('examenes_suficiencia.update_puntaje', $detalle->id)}}">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /editPuntajeModal -->
@endforeach
