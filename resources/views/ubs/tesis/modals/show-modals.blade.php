<!-- approveCalidadModal -->
    @can('aprobar_calidad_tesis_ubs')
        <div class="modal fade flip" id="approveCalidadModal" tabindex="-1" aria-labelledby="approveCalidadModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Aprobar Tesis</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('tesis_ubs.aprobar_calidad', $tesis->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de aprobar el tema de la tesis del alumno {{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, aprobar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /approveCalidadModal -->

<!-- rejectModal -->
    @can('rechazar_tesis_ubs')
        <div class="modal fade flip" id="rejectModal" tabindex="-1" aria-labelledby="rejectModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Rechazar Tesis</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('tesis_ubs.rechazar', $tesis->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de rechazar el tema de la tesis del alumno {{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, rechazar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /rejectModal -->

<!-- unrejectModal -->
    @can('anular_rechazo_tesis_ubs')
        <div class="modal fade flip" id="unrejectModal" tabindex="-1" aria-labelledby="unrejectModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Anular Rechazo del Tesis</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('tesis_ubs.anular_rechazo', $tesis->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de anular el rechazo del tema de la tesis del alumno {{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, anular!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /unrejectModal -->

<!-- unapproveCalidadModal -->
    @can('anular_aprobacion_calidad_tesis_ubs')
        <div class="modal fade flip" id="unapproveCalidadModal" tabindex="-1" aria-labelledby="unapproveCalidadModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Anular Aprobación de Tesis</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('tesis_ubs.anular_aprobacion_calidad', $tesis->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de anular la aprobación de coordinación del tema de la tesis del alumno {{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, anular!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /unapproveCalidadModal -->

<!-- approveTutorModal -->
    @can('aprobar_tutori_tesis_ubs')
        <div class="modal fade flip" id="approveTutorModal" tabindex="-1" aria-labelledby="approveTutorModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Aprobar Tesis</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('tesis_ubs.aprobar_tutor', $tesis->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de aprobar el tema de la tesis del alumno {{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, aprobar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /approveTutorModal -->

<!-- unapproveTutorModal -->
    @can('anular_aprobacion_tutor_tesis_ubs')
        <div class="modal fade flip" id="unapproveTutorModal" tabindex="-1" aria-labelledby="unapproveTutorModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Anular Aprobación de Tesis</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('tesis_ubs.anular_aprobacion_tutor', $tesis->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de anular la aprobación del tutor del tema de la tesis del alumno {{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, anular!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /unapproveTutorModal -->

<!-- asignarFechaDefensaModal -->
    @can('asignar_fecha_defensa_tesis_ubs')
        <div class="modal fade flip" id="asignarFechaDefensaModal" tabindex="-1" aria-labelledby="asignarFechaDefensaModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="asignarFechaDefensaModal">Asignar Fecha de Defensa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('tesis_ubs.asignar_fecha_defensa', $tesis->id)}}" method="post" id="asignar-fecha-defensa-form">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($fechas_defensas as $fecha)
                                            <option value="{{$fecha->id}}" @if (old('fecha') == strval($fecha->id)) selected @endif>{{\Carbon\Carbon::parse($fecha->fecha)->format('d/m/Y')}} {{$fecha->hora}}</option>
                                        @endforeach
                                    </select>
                                    @error('fecha')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success asignar-fecha-defensa-btn" id="asignar-fecha-defensa-btn" data-id="{{$tesis->id}}" data-url="{{route('tesis_ubs.asignar_fecha_defensa', $tesis->id)}}">Asignar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /asignarFechaDefensaModal -->

<!-- puntuarDefensaModal -->
    @can('puntuar_defensa_tesis_ubs')
        <div class="modal fade flip" id="puntuarDefensaModal" tabindex="-1" aria-labelledby="puntuarDefensaModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="puntuarDefensaModal">Puntuar Defensa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('tesis_ubs.puntuar_defensa', $tesis->id)}}" method="post" id="puntuar-defensa-form">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="fecha">Puntaje <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center @error('puntaje_obtenido') is-invalid @enderror" id="puntaje_obtenido" name="puntaje_obtenido" placeholder="1-100">
                                    @error('puntaje_obtenido')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="calificacion">Calificación</label>
                                    <input type="text" class="form-control text-center" id="calificacion" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success puntuar-defensa-btn" id="puntuar-defensa-btn" data-id="{{$tesis->id}}" data-url="{{route('tesis_ubs.puntuar_defensa', $tesis->id)}}">Puntuar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /puntuarDefensaModal -->
