@foreach ($asistencias as $asistencia)
    <!-- observacionModal -->
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
    <!-- /observacionModal -->
@endforeach
