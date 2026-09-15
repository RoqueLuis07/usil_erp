<!-- moduloNotasModal -->
    @can('crear_notas_maestrias_ubs')
        <div class="modal fade flip" id="moduloNotasModal" tabindex="-1" aria-labelledby="moduloNotasModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h4 class="modal-title">Seleccionar Módulo</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="modulo">Módulo</label>
                                    <select class="form-control selectpicker modulo" id="modulo" name="modulo" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($maestria->modulos as $modulo)
                                                <option value="{{$modulo->modulo->id}}" data-subtext="{{$modulo->modulo->nombre_real}}">{{$modulo->modulo->nombre_fantasia}}</option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="evaluacion">Evaluación</label>
                                    <select class="form-control selectpicker evaluacion" id="evaluacion" name="evaluacion" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-success cargar-btn" data-id="{{$maestria->id}}">Cargar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /moduloNotasModal -->

@foreach ($maestria->notas as $nota)
    <!-- notaDetalleModal -->
    <div class="modal fade flip" id="notaDetalleModal-{{$nota->id}}" tabindex="-1" aria-labelledby="notaDetalleModal-{{$nota->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="card-title">Puntajes de: {{$nota->alumno->primer_nombre}} {{$nota->alumno->primer_apellido}} - <span class="text-muted">{{$nota->alumno->numero_documento}}</span></h5>
                </div>
                <div class="modal-body">
                    @php
                        $proceso = 'N/A';
                        $final = 'N/A';
                        $complementario = 'N/A';
                        $extraordinario = 'N/A';
                    @endphp
                    @foreach ($maestria->puntajes as $puntaje)
                    @if ($puntaje->modulo_id == $nota->modulo_id && $puntaje->alumno_id == $nota->alumno_id)
                        @if ($puntaje->evaluacion_id == 1)
                            @php
                                if ($puntaje->puntos_obtenidos >= 0) {
                                    $proceso = $puntaje->puntos_obtenidos;
                                }
                            @endphp
                        @elseif ($puntaje->evaluacion_id == 2)
                            @php
                                if ($puntaje->puntos_obtenidos >= 0) {
                                    $final = $puntaje->puntos_obtenidos;
                                    if ($puntaje->observaciones == 'AUSENTE') {
                                        $final = $final . ' (' . $puntaje->observaciones . ')';
                                    }
                                }
                            @endphp
                        @elseif ($puntaje->evaluacion_id == 3)
                            @php
                                if ($puntaje->puntos_obtenidos >= 0) {
                                    $complementario = $puntaje->puntos_obtenidos;
                                    if ($puntaje->observaciones == 'AUSENTE') {
                                        $complementario = $complementario . ' (' . $puntaje->observaciones . ')';
                                    }
                                }
                            @endphp
                        @elseif ($puntaje->evaluacion_id == 4)
                            @php
                                if ($puntaje->puntos_obtenidos >= 0) {
                                    $extraordinario = $puntaje->puntos_obtenidos;
                                    if ($puntaje->observaciones == 'AUSENTE') {
                                        $extraordinario = $extraordinario . ' (' . $puntaje->observaciones . ')';
                                    }
                                }
                            @endphp
                        @endif
                    @endif
                    @endforeach
                    <div class="row justify-content-center">
                        <div class="col-lg-6 mb-3 text-center">
                            <label class="form-label" for="proceso">Proceso</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" id="proceso" value="{{$proceso}}" readonly>
                                @if ($proceso != 'N/A') <span class="input-group-text">puntos</span> @endif
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3 text-center">
                            <label class="form-label" for="final">Ordinario</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" id="final" value="{{$final}}" readonly>
                                @if ($final != 'N/A') <span class="input-group-text">puntos</span> @endif
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-6 mb-3 text-center">
                            <label class="form-label" for="complementario">Complementario</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" id="complementario" value="{{$complementario}}" readonly>
                                @if ($complementario != 'N/A') <span class="input-group-text">puntos</span> @endif
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3 text-center">
                            <label class="form-label" for="extraordinario">Extraordinario</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" id="extraordinario" value="{{$extraordinario}}" readonly>
                                @if ($extraordinario != 'N/A') <span class="input-group-text">puntos</span> @endif
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 mb-3 text-center">
                            <label class="form-label" for="calificacion">Calificación</label>
                            <input type="text" class="form-control text-center" id="calificacion" value="{{$nota->calificacion}}" readonly>
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
    <!-- /notaDetalleModal -->
@endforeach
