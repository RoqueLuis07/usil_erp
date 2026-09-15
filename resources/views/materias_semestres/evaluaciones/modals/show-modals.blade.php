@foreach ($alumnos as $alumno)
    @foreach ($alumno->alumnoPuntajes as $puntaje)
        <!-- editEvaluacionModal -->
            @can('editar_puntajes_evaluaciones_materias_semestres')
                <div class="modal fade flip" id="editEvaluacionModal-{{$puntaje->id}}" tabindex="-1" aria-labelledby="editEvaluacionModal-{{$puntaje->id}}" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="editEvaluacionModal-{{$puntaje->id}}">Editar Puntaje</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{route('materias_evaluaciones.update', $puntaje->id)}}" method="post" id="update-form-{{$puntaje->id}}">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="alumno-{{$puntaje->id}}">Alumno</label>
                                            <input type="text" class="form-control" id="alumno-{{$puntaje->id}}" value="{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="evaluacion={{$puntaje->id}}">Evaluación</label>
                                            <input type="text" class="form-control" id="evaluacion-{{$puntaje->id}}" value="{{$puntaje->evaluacion->nombre}}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="puntos_posibles-{{$puntaje->id}}">Puntos Posibles</label>
                                            <input type="text" class="form-control" id="puntos_posibles-{{$puntaje->id}}" name="puntos_posibles" value="{{$puntaje->evaluacion->puntos}}" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="puntos_obtenidos-{{$puntaje->id}}">Puntos Obtenidos</label>
                                            <input type="text" class="form-control" id="puntos_obtenidos-{{$puntaje->id}}" name="puntos_obtenidos" value="{{$puntaje->puntos_obtenidos}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger cancel-btn" data-id="{{$puntaje->id}}">Cancelar</button>
                                    <button type="button" class="btn btn-success update-btn" data-id="{{$puntaje->id}}" data-url="{{route('materias_evaluaciones.update', $puntaje->id)}}">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        <!-- /editEvaluacionModal -->
    @endforeach
@endforeach
