<!-- cargarInformeModal -->
    @can('cargar_informes_extensiones_universitarias')
        <div class="modal fade flip" id="cargarInformeModal-{{$extension->id}}" tabindex="-1" aria-labelledby="cargarInformeModal-{{$extension->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="cargarInformeModal-{{$extension->id}}">Cargar Informe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cargarInforme-form-{{$extension->id}}" action="{{route('extensiones_universitarias.cargar_informe', $extension->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12" id="div-informe">
                                    <label class="form-label" for="informe">Informe <span class="text-danger">(*)</span></label>
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('informe') is-invalid @enderror" id="informe" name="informe" accept="application/pdf">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-informe" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('informe')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <p class="text-muted" style="font-size: 14px">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-lg-12 mb-4">
                                    <label class="form-label" for="informe-ejemplo">Plantilla de Ejemplo</label>
                                    <div>
                                        <a type="button" class="btn btn-secondary" href="{{asset('storage/extensiones_universitarias/plantillas/informe-final.xlsx')}}" download="informe-final.xlsx"><i class="ri-download-line align-bottom mb-0 me-2"></i> Descargar</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-btn" data-id="{{$extension->id}}" data-url="{{route('extensiones_universitarias.cargar_informe', $extension->id)}}">Cargar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /cargarInformeModal -->

<!-- cambiarAdjuntosModal -->
    @if (Auth::user()->can('cambiar_adjunto_proyectos_extensiones_universitarias') || Auth::user()->can('cambiar_adjunto_informes_extensiones_universitarias'))
        <div class="modal fade flip" id="cambiarAdjuntosModal-{{$extension->id}}" tabindex="-1" aria-labelledby="cambiarAdjuntosModal-{{$extension->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title titulo_adjuntos" id="cambiarAdjuntosModal-{{$extension->id}}"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cambiarAdjuntos-form-{{$extension->id}}" action="{{route('extensiones_universitarias.cargar_informe', $extension->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3" id="div-archivo">
                                    <label class="form-label label_adjuntos" for="archivo"></label>
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('archivo') is-invalid @enderror" id="archivo" name="archivo" accept="application/pdf">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-archivo" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('archivo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <p class="text-muted" style="font-size: 14px">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                </div>
                            </div>
                            <input type="hidden" id="tipo_adjunto" name="tipo_adjunto" value="">
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-adjuntos-btn" data-id="{{$extension->id}}" data-url="{{route('extensiones_universitarias.change_adjuntos', $extension->id)}}">Cargar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
<!-- /cambiarAdjuntosModal -->
