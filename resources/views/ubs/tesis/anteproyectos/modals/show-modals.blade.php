@foreach ($tesis->anteproyectos as $anteproyecto)
    <!-- approveTutorModal -->
        @can('aprobar_tutor_anteproyectos_tesis_ubs')
            <div class="modal fade flip" id="approveTutorModal-{{$anteproyecto->id}}" tabindex="-1" aria-labelledby="approveTutorModal-{{$anteproyecto->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form action="{{route('tesis_ubs.aprobar_tutor_anteproyecto', $anteproyecto->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de aprobar el bloque {{$anteproyecto->bloque->nombre}} ?</h4>
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

    <!-- desapproveTutorModal -->
        @can('anular_aprobacion_tutor_anteproyectos_tesis_ubs')
            <div class="modal fade flip" id="desapproveTutorModal-{{$anteproyecto->id}}" tabindex="-1" aria-labelledby="desapproveTutorModal-{{$anteproyecto->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form action="{{route('tesis_ubs.anular_aprobacion_tutor_anteproyecto', $anteproyecto->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular la aprobación del bloque {{$anteproyecto->bloque->nombre}} ?</h4>
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
    <!-- /desapproveTutorModal -->

    <!-- approveCalidadModal -->
        @can('aprobar_calidad_anteproyectos_tesis_ubs')
            <div class="modal fade flip" id="approveCalidadModal-{{$anteproyecto->id}}" tabindex="-1" aria-labelledby="approveCalidadModal-{{$anteproyecto->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="approveCalidadModal-{{$anteproyecto->id}}">Asignar Puntaje</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('tesis_ubs.aprobar_calidad_anteproyecto', $anteproyecto->id)}}" method="post" id="approve-calidad-form-{{$anteproyecto->id}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="puntaje_obtenido">Puntaje <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center puntaje_obtenido @error('puntaje_obtenido') is-invalid @enderror" id="puntaje_obtenido-{{$anteproyecto->id}}" name="puntaje_obtenido" value="{{old('puntaje_obtenido')}}" placeholder="0-100" data-id="{{$anteproyecto->id}}">
                                        @error('puntaje_obtenido')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="calificacion">Calificación <span class="text-danger"></span></label>
                                        <input type="text" class="form-control text-center" id="calificacion-{{$anteproyecto->id}}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success approve-calidad-btn" id="approve-calidad-btn-{{$anteproyecto->id}}" data-id="{{$anteproyecto->id}}" data-url="{{route('tesis_ubs.aprobar_calidad_anteproyecto', $anteproyecto->id)}}">Aprobar</button>
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

    <!-- desapproveCalidadModal -->
        @can('anular_aprobacion_calidad_anteproyectos_tesis_ubs')
            <div class="modal fade flip" id="desapproveCalidadModal-{{$anteproyecto->id}}" tabindex="-1" aria-labelledby="desapproveCalidadModal-{{$anteproyecto->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form action="{{route('tesis_ubs.anular_aprobacion_calidad_anteproyecto', $anteproyecto->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular la aprobación del bloque {{$anteproyecto->bloque->nombre}} ?</h4>
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
    <!-- /desapproveCalidadModal -->
@endforeach
