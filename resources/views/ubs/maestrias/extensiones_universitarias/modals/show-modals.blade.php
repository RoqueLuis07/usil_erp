<!-- cargarInformeModal -->
    @can('cargar_informes_extensiones_ubs')
        <div class="modal fade flip" id="cargarInformeModal-{{$extension->id}}" tabindex="-1" aria-labelledby="cargarInformeModal-{{$extension->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="cargarInformeModal-{{$extension->id}}">Cargar Informe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cargarInforme-form-{{$extension->id}}" action="{{route('extensiones_universitarias_ubs.cargar_informe', $extension->id)}}" method="post" enctype="multipart/form-data">
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
                                        <a type="button" class="btn btn-secondary" href="{{asset('storage/maestrias/extensiones_universitarias/plantillas/informe-final.xlsx')}}" download="informe-final.xlsx"><i class="ri-download-line align-bottom mb-0 me-2"></i> Descargar</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="save-btn" data-id="{{$extension->id}}" data-url="{{route('extensiones_universitarias_ubs.cargar_informe', $extension->id)}}">Cargar</button>
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
    @if (Auth::user()->can('cambiar_adjunto_proyectos_extensiones_ubs') || Auth::user()->can('cambiar_adjunto_informes_extensiones_ubs'))
        <div class="modal fade flip" id="cambiarAdjuntosModal-{{$extension->id}}" tabindex="-1" aria-labelledby="cambiarAdjuntosModal-{{$extension->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title titulo_adjuntos" id="cambiarAdjuntosModal-{{$extension->id}}"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cambiarAdjuntos-form-{{$extension->id}}" action="{{route('extensiones_universitarias_ubs.change_adjuntos', $extension->id)}}" method="post" enctype="multipart/form-data">
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
                                    <button type="button" class="btn btn-success" id="save-adjuntos-btn" data-id="{{$extension->id}}" data-url="{{route('extensiones_universitarias_ubs.change_adjuntos', $extension->id)}}">Cargar</button>
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

@can('editar_horas_alumnos_extensiones_ubs')
    @if ($extension->estado == 'IN')
    <!-- horasModal -->
    <div class="modal fade flip" id="horasModal" tabindex="-1" aria-labelledby="horasModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h4 class="modal-title">Cargar Horas</h4>
                </div>
                <div class="modal-body">
                    <form id="horas-form" action="{{route('extensiones_universitarias_ubs.update_hours', $extension->id)}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="alumno">Alumno</label>
                                <input type="text" class="form-control" value="{{$extension->alumno->primer_nombre}} {{$extension->alumno->primer_apellido}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="horas">Horas Realizadas <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control text-center @error('horas') is-invalid @enderror" id="horas" name="horas" value="{{old('horas')}}">
                                @error('horas')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="hstack gap-2 justify-content-center">
                                <button type="button" class="btn btn-success" id="cargar-horas-btn" data-id="{{$extension->id}}" data-url="{{route('extensiones_universitarias_ubs.update_hours', $extension->id)}}">Guardar</button>
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /horasModal -->
    @endif

    @if ($extension->estado == 'CO')
    <!-- changeHorasModal -->
    <div class="modal fade flip" id="changeHorasModal" tabindex="-1" aria-labelledby="changeHorasModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h4 class="modal-title">Editar Horas</h4>
                </div>
                <div class="modal-body">
                    <form id="change-horas-form" action="{{route('extensiones_universitarias_ubs.update_hours', $extension->id)}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="alumno">Alumno</label>
                                <input type="text" class="form-control" value="{{$extension->alumno->primer_nombre}} {{$extension->alumno->primer_apellido}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="horas">Horas Realizadas <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control text-center @error('horas') is-invalid @enderror" id="horas" name="horas" value="{{old('horas', number_format($extension->alumno_horas_realizadas, 0, ',', '.'))}}">
                                @error('horas')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="hstack gap-2 justify-content-center">
                                <button type="button" class="btn btn-success" id="editar-horas-btn" data-id="{{$extension->id}}" data-url="{{route('extensiones_universitarias_ubs.update_hours', $extension->id)}}">Actualizar</button>
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /horasModal -->
    @endif
@endcan
