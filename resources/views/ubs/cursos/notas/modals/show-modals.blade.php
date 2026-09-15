@foreach ($curso->notas as $nota)
    <!-- editPuntajeModal -->
        @can('editar_notas_cursos_ubs')
            <div class="modal fade flip" id="editPuntajeModal-{{$nota->id}}" tabindex="-1" aria-labelledby="editPuntajeModal-{{$nota->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="card-title">Nota de: {{$nota->alumno->primer_nombre}} {{$nota->alumno->primer_apellido}} - <span class="text-muted">{{$nota->alumno->numero_documento}}</span></h5>
                        </div>
                        <form action="{{route('cursos_notas_ubs.update', $nota->id)}}" method="post" id="edit-puntaje-form-{{$nota->id}}">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="puntaje_obtenido">Puntos</label>
                                        <input class="form-control text-center puntaje_obtenido" id="puntaje_obtenido-{{$nota->id}}" name="puntaje_obtenido" value="{{$nota->puntaje_obtenido}}" data-id="{{$nota->id}}"></input>
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="calificacion">Calificación</label>
                                        <input class="form-control text-center" id="calificacion-{{$nota->id}}" value="{{$nota->calificacion}}" readonly></input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success edit-puntaje" data-id="{{$nota->id}}" data-url="{{route('cursos_notas_ubs.update', $nota->id)}}">Actualizar</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /editPuntajeModal -->
@endforeach
