@foreach ($tutoria->alumnos as $detalle)
    <!-- showDetalleAlumnoModal -->
        @can('ver_puntajes_alumnos_tutorias')
            <div class="modal fade flip" id="showDetalleAlumnoModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="showDetalleAlumnoModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="showDetalleAlumnoModal-{{$detalle->id}}">Ver Detalle de Alumno</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-8 mb-3">
                                    <label class="form-label" for="alumno_modal">Alumno</label>
                                    <input type="text" class="form-control" id="alumno_modal" value="{{$detalle->alumno->primer_nombre}} {{$detalle->alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="documento_alumno_modal">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="documento_alumno_modal" value="{{number_format($detalle->alumno->numero_documento, 0, ',', '.')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3 text-center">
                                    <label class="form-label" for="asistencia">Asistencia</label>
                                    <input type="text" class="form-control text-center" id="asistencia" @if ($detalle->cantidad_asistencias > 0) value="{{($detalle->cantidad_asistencias / $tutoria->cantidad_clases) * 100}}%" @else value="PENDIENTE" @endif readonly>
                                </div>
                                <div class="col-lg-4 mb-3 text-center">
                                    <label class="form-label" for="asistencia">Puntaje</label>
                                    <input type="text" class="form-control text-center" id="asistencia" @if ($detalle->puntos_obtenidos) value="{{$detalle->puntos_obtenidos}}" @else value="PENDIENTE" @endif readonly>
                                </div>
                                <div class="col-lg-4 mb-3 text-center">
                                    <label class="form-label" for="calificacion">Calificación</label>
                                    <input type="text" class="form-control text-center" id="calificacion" @if ($detalle->calificacion) value="{{$detalle->calificacion}}" @else value="PENDIENTE" @endif readonly>
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
    <!-- /showDetalleAlumnoModal -->
@endforeach
