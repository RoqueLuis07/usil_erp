@if ($solicitud->tipo_solicitud_id != 2)
    <!-- approveModal -->
        @can('aprobar_solicitudes')
            <div class="modal fade flip" id="approveModal" tabindex="-1" aria-labelledby="approveModal" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="approve-form" action="{{route('solicitudes.approve', $solicitud->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de aprobar la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
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
    <!-- /approveModal -->
@else
    <!-- approveModal -->
        @can('aprobar_solicitudes')
            <div class="modal fade flip" id="approveModal" tabindex="-1" aria-labelledby="approveModal" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="showModal">Aprobar Solicitud</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="approve-form" action="{{route('solicitudes.approve', $solicitud->id)}}" method="post">
                                @csrf
                                <input type="hidden" name="tipo" value="SUFICIENCIA">
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_examen">Fecha de Exámen <span class="text-danger">(*)</span></label>
                                        <div class="form-icon right">
                                            <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_examen') is-invalid @enderror" id="fecha_examen" name="fecha_examen" value="{{old('fecha_examen')}}">
                                            <i class="ri-calendar-2-line" id="calendar-icon-fecha_examen"></i>
                                            @error('fecha_examen')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="modalidad">Modalidad <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($modalidades as $modalidad)
                                                <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @error('modalidad')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <label class="form-label" for="docente">Docente <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($docentes as $docente)
                                                <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                            @endforeach
                                        </select>
                                        @error('docente')
                                            <span class="invalid feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success" id="save-suficiencia-btn" data-id="{{$solicitud->id}}" data-url="{{route('solicitudes.approve', $solicitud->id)}}">Sí, guardar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /approveModal -->
@endif

<!-- paraRetiroModal -->
    @can('retirar_solicitudes')
        <div class="modal fade flip" id="paraRetiroModal" tabindex="-1" aria-labelledby="paraRetiroModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="approve-form" action="{{route('solicitudes.para_entrega', $solicitud->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de cambiar el estado de la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, cambiar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /paraRetiroModal -->

<!-- rejectModal -->
    @can('rechazar_solicitudes')
        <div class="modal fade flip" id="rejectModal" tabindex="-1" aria-labelledby="rejectModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="reject-form" action="{{route('solicitudes.reject', $solicitud->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de rechazar la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
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

<!-- unapproveModal -->
    @can('anular_aprobacion_solicitudes')
        <div class="modal fade flip" id="unapproveModal" tabindex="-1" aria-labelledby="unapproveModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="unapprove-form" action="{{route('solicitudes.unapprove', $solicitud->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de anular la aprobación de la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
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
<!-- /unapproveModal -->

<!-- unrejectModal -->
    @can('anular_rechazo_solicitudes')
        <div class="modal fade flip" id="unrejectModal" tabindex="-1" aria-labelledby="unrejectModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="unreject-form" action="{{route('solicitudes.unreject', $solicitud->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de volver a pendiente la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, volver!</button>
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

<!-- deliverModal -->
    @can('entregar_solicitudes')
        <div class="modal fade flip" id="deliverModal" tabindex="-1" aria-labelledby="deliverModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="deliver-form" action="{{route('solicitudes.deliver', $solicitud->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de entregar la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, entregar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /deliverModal -->

<!-- undeliverModal -->
    @can('anular_entrega_solicitudes')
        <div class="modal fade flip" id="undeliverModal" tabindex="-1" aria-labelledby="undeliverModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="undeliver-form" action="{{route('solicitudes.undeliver', $solicitud->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de anular la entrega de la solicitud {{$solicitud->tipoSolicitud->nombre}} del alumno {{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}?</h4>
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
<!-- /undeliverModal -->
