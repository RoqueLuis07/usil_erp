@foreach ($clase->asistencias as $key => $asistencia)
    <!-- observacionModal -->
        @can('editar_asistencias_alumnos_tutorias')
            <div class="modal fade flip" id="observacionModal-{{$asistencia->id}}" tabindex="-1" aria-labelledby="observacionModal-{{$asistencia->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="card-title">Asistencia de: {{$asistencia->alumno->primer_nombre}} {{$asistencia->alumno->primer_apellido}} - <span class="text-muted">{{$asistencia->alumno->numero_documento}}</span></h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <label class="form-label" for="observacion">Observación</label>
                                    <textarea class="form-control text-center" id="observacion" cols="30" rows="3" readonly>{{$asistencia->observaciones}}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /observacionModal -->

    <!-- cambiarAsistenciaModal -->
        @can('editar_asistencias_alumnos_tutorias')
            <div class="modal fade flip" id="cambiarAsistenciaModal-{{$asistencia->id}}" tabindex="-1" aria-labelledby="cambiarAsistenciaModal-{{$asistencia->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title">Asistencia de: {{$asistencia->alumno->primer_nombre}} {{$asistencia->alumno->primer_apellido}} - <span class="text-muted">{{$asistencia->alumno->numero_documento}}</span></h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('tutorias_clases.update', $asistencia->id)}}" method="post" id="change-asistencia-form-{{$asistencia->id}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <label class="form-label" for="estado">Asistencia</label>
                                        <div class="mb-3">
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check btn-asistencia" id="btn-asistencia1-{{$asistencia->id}}" value="AU" data-id="{{$asistencia->id}}" @if ($asistencia->estado == 'AU') checked @endif>
                                                <label class="btn btn-outline-danger" for="btn-asistencia1-{{$asistencia->id}}">Asusente</label>
                                                <input type="radio" class="btn-check btn-asistencia" id="btn-asistencia2-{{$asistencia->id}}" value="PR" data-id="{{$asistencia->id}}" @if ($asistencia->estado == 'PR') checked @endif>
                                                <label class="btn btn-outline-success" for="btn-asistencia2-{{$asistencia->id}}">Presente</label>
                                                <input type="radio" class="btn-check btn-asistencia" id="btn-asistencia3-{{$asistencia->id}}" value="AJ" data-id="{{$asistencia->id}}" @if ($asistencia->estado == 'AJ') checked @endif>
                                                <label class="btn btn-outline-warning" for="btn-asistencia3-{{$asistencia->id}}">Justificado</label>
                                            </div>
                                            <input type="hidden" id="estado-{{$asistencia->id}}" name="estado" value="{{$asistencia->estado}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success change-asistencia" data-id="{{$asistencia->id}}" data-url="{{route('tutorias_clases.update', $asistencia->id)}}">Cambiar</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /cambiarAsistenciaModal -->

    <!-- changeObservacionModal -->
        @can('editar_asistencias_alumnos_tutorias')
            <div class="modal fade flip" id="changeObservacionModal-{{$asistencia->id}}" tabindex="-1" aria-labelledby="changeObservacionModal-{{$asistencia->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="card-title">Asistencia de: {{$asistencia->alumno->primer_nombre}} {{$asistencia->alumno->primer_apellido}} - <span class="text-muted">{{$asistencia->alumno->numero_documento}}</span></h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('tutorias_clases.update_observacion', $asistencia->id)}}" method="post" id="change-observacion-form-{{$asistencia->id}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <label class="form-label" for="observaciones">Observaciones</label>
                                        <textarea class="form-control text-center" id="observaciones" name="observaciones" cols="30" rows="3">{{$asistencia->observaciones}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success change-observacion" data-id="{{$asistencia->id}}" data-url="{{route('tutorias_clases.update_observacion', $asistencia->id)}}">Actualizar</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /changeObservacionModal -->
@endforeach
