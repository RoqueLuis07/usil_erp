<!-- addEntregaModal -->
    @can('entregar_anteproyectos_tesis')
        <div class="modal fade flip" id="addEntregaModal" tabindex="-1" aria-labelledby="addEntregaModal" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h4 class="modal-title">Nueva Entrega</h4>
                    </div>
                    <div class="modal-body">
                        <form id="add-entrega-form" action="{{route('anteproyectos_tesis.save_entrega', $anteproyecto->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mt-3" id="div-archivo">
                                    <label class="form-label" for="archivo">Documento <span class="text-danger">(*)</span></label>
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('archivo') is-invalid @enderror" id="archivo-entrega" name="archivo" accept=".doc,.docx">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-archivo-entrega" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('archivo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <p class="text-muted">Se aceptan archivos del tipo <code>.doc</code>, <code>.docx</code>.</p>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="comentario">Comentario</label>
                                    <textarea class="form-control" id="comentario" name="comentario" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-entrega-btn" data-id="{{$anteproyecto->id}}">Guardar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /addEntregaModal -->

<!-- addCorreccionModal -->
    @can('corregir_anteproyectos_tesis')
        <div class="modal fade flip" id="addCorreccionModal" tabindex="-1" aria-labelledby="addCorreccionModal" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h4 class="modal-title">Agregar Corrección / Comentario</h4>
                    </div>
                    <div class="modal-body">
                        <form id="add-correccion-form" action="{{route('anteproyectos_tesis.save_correccion', $anteproyecto->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mt-3" id="div-archivo">
                                    <label class="form-label" for="archivo">Documento</label>
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('archivo') is-invalid @enderror" id="archivo-correccion" name="archivo" accept=".doc,.docx">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-archivo-correccion" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('archivo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <p class="text-muted">Se aceptan archivos del tipo <code>.doc</code>, <code>.docx</code>.</p>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="comentario">Comentario</label>
                                    <textarea class="form-control" id="comentario" name="comentario" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-correccion-btn" data-id="{{$anteproyecto->id}}">Guardar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /addCorreccionModal -->

@foreach ($anteproyecto->entregas as $entrega)
    <!-- entregaModal -->
    <div class="modal fade flip" id="entregaModal-{{$entrega->id}}" tabindex="-1" aria-labelledby="entregaModal-{{$entrega->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h4 class="modal-title">Ver Detalle de @if ($entrega->entrega == false) Corección/Comentario @else Entrega @endif</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-lg-5 mb-3">
                                <label class="form*-label" for="fecha">Fecha</label>
                                <input type="text" class="form-control text-center" id="fecha" value="{{\Carbon\Carbon::parse($entrega->created_at)->format('d/m/Y H:m')}}" readonly>
                            </div>
                            <div class="col-lg-7 mb-3">
                                <label class="form*-label" for="autor">@if ($entrega->entrega == false) Tutor @else Alumno @endif</label>
                                <input type="text" class="form-control" id="autor" @if ($entrega->entrega == false) value="{{$entrega->anteproyecto->inscripcion->tutor->primer_nombre}} {{$entrega->anteproyecto->inscripcion->tutor->primer_apellido}}" @else value="{{$entrega->anteproyecto->inscripcion->alumno->primer_nombre}} {{$entrega->anteproyecto->inscripcion->alumno->primer_apellido}}" @endif readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label class="form-label" for="comentario">Comentario</label>
                                <textarea class="form-control" id="comentario" cols="30" rows="3" readonly>{{$entrega->comentario}}</textarea>
                            </div>
                        </div>
                        @if ($entrega->url_archivo)
                            <hr>
                            <div class="row text-center">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="archivo">Documento</label>
                                    <div>
                                        @php
                                            if ($entrega->entrega == false) {
                                                $tipo = 'correccion_';

                                            } else {
                                                $tipo = 'entrega_';
                                            }
                                            $nombre = $tipo . $entrega->numero . '_anteproyecto_bloque_' . $entrega->anteproyecto->bloque->numero;
                                            $nombre_archivo = $nombre . $entrega->extension_archivo;
                                        @endphp
                                        <a href="{{asset($entrega->url_archivo)}}" download="{{$nombre_archivo}}">
                                            <img src="{{asset('storage/word.png')}}" alt="Documento" width="100px">
                                        </a>
                                    </div>
                                    <p class="text-muted">Haga click en el icono para descargar el archivo.</p>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="hstack gap-2 justify-content-center">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /entregaModal -->

    <!-- deleteEntregaModal -->
        @can('eliminar_entregas_anteproyectos_tesis')
            <div class="modal fade flip" id="deleteEntregaModal-{{$entrega->id}}" tabindex="-1" aria-labelledby="deleteEntregaModal-{{$entrega->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form action="{{route('anteproyectos_tesis.delete_entrega', $entrega->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        @php
                                            if ($entrega->entrega == false) {
                                                $tipo = 'correción';
                                            } else {
                                                $tipo = 'entrega';
                                            }
                                        @endphp
                                        <h4>¿Está seguro de eliminar la {{$tipo}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, eliminar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /deleteEntregaModal -->
@endforeach
