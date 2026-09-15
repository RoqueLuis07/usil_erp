@can('ver_puntajes_alumnos_pantalla')
    @foreach ($inscripciones as $inscripcion)
        <!-- puntajesModal -->
            <div class="modal fade flip" id="puntajesModal-{{$inscripcion->id}}" tabindex="-1" aria-labelledby="puntajesModal-{{$inscripcion->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="puntajesModal-{{$inscripcion->id}}">Mis Puntajes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="materia-{{ $inscripcion->id }}">Materia</label>
                                    <input type="text" class="form-control" id="materia-{{ $inscripcion->id }}" value="{{ $inscripcion->materia->nombre_fantasia }}" readonly>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="proceso-{{ $inscripcion->id }}">Proceso</label>
                                    <input type="text" class="form-control text-center" id="proceso-{{ $inscripcion->id }}" value="@if ($inscripcion->puntaje_proceso) {{ $inscripcion->puntaje_proceso }} @else --- @endif" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="parcial-{{ $inscripcion->id }}">Parcial</label>
                                    <input type="text" class="form-control text-center" id="parcial-{{ $inscripcion->id }}" value="@if ($inscripcion->puntaje_parcial) {{ $inscripcion->puntaje_parcial }} @else --- @endif" readonly>
                                </div>
                                {{-- <div class="col-lg-4 mb-3 text-center">
                                    <label class="form-label" for="recuperatorio-{{ $inscripcion->id }}">Recuperatorio</label>
                                    <input type="text" class="form-control text-center" id="recuperatorio-{{ $inscripcion->id }}" value="@if ($inscripcion->puntaje_recuperatorio) {{ $inscripcion->puntaje_recuperatorio }} @else --- @endif" readonly>
                                </div> --}}
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="tipo_final-{{ $inscripcion->id }}">Evaluación</label>
                                    <input type="text" class="form-control text-center" id="tipo_final-{{ $inscripcion->id }}" value="@if ($inscripcion->puntaje_ordinario) ORDINARIO @elseif ($inscripcion->puntaje_complementario) COMPLEMENTARIO @elseif ($inscripcion->puntaje_extraordinario) EXTRAORDINARIO @else --- @endif" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="nota_final-{{ $inscripcion->id }}">Final</label>
                                    <input type="text" class="form-control text-center" id="nota_final-{{ $inscripcion->id }}" value="@if ($inscripcion->puntaje_ordinario) {{ $inscripcion->puntaje_ordinario }} @elseif ($inscripcion->puntaje_complementario) {{ $inscripcion->puntaje_complementario }} @elseif ($inscripcion->puntaje_extraordinario) {{ $inscripcion->puntaje_extraordinario }} @else --- @endif" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /puntajesModal -->
    @endforeach
@endcan
