<!-- approveCoordinacionModal -->
    @can('aprobar_coordinacion_inscripciones_tesis')
        <div class="modal fade flip" id="approveCoordinacionModal" tabindex="-1" aria-labelledby="approveCoordinacionModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Aprobar Tema de Trabajo Final de Grado</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('inscripciones_temas_tesis.aprobar_coordinacion', $inscripcion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de aprobar el tema de trabajo final de grado del alumno {{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</h4>
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
<!-- /approveCoordinacionModal -->

<!-- rejectModal -->
    @can('rechazar_inscripciones_tesis')
        <div class="modal fade flip" id="rejectModal" tabindex="-1" aria-labelledby="rejectModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Rechazar Tema de Trabajo Final de Grado</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('inscripciones_temas_tesis.rechazar', $inscripcion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de rechazar el tema de trabajo final de grado del alumno {{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</h4>
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
    @can('anular_rechazo_inscripciones_tesis')
        <div class="modal fade flip" id="unrejectModal" tabindex="-1" aria-labelledby="unrejectModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Anular Rechazo del Tema de Trabajo Final de Grado</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('inscripciones_temas_tesis.anular_rechazo', $inscripcion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de anular el rechazo del tema de trabajo final de grado del alumno {{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</h4>
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

<!-- unapproveCoordinacionModal -->
    @can('anular_coordinacion_inscripciones_tesis')
        <div class="modal fade flip" id="unapproveCoordinacionModal" tabindex="-1" aria-labelledby="unapproveCoordinacionModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Anular Aprobación de Tema de Trabajo Final de Grado</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('inscripciones_temas_tesis.anular_aprobacion_coordinacion', $inscripcion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de anular la aprobación de coordinación del tema de trabajo final de grado del alumno {{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</h4>
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
<!-- /unapproveCoordinacionModal -->

<!-- approveTutorModal -->
    @can('aprobar_tutor_inscripciones_tesis')
        <div class="modal fade flip" id="approveTutorModal" tabindex="-1" aria-labelledby="approveTutorModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Aprobar Tema de Trabajo Final de Grado</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('inscripciones_temas_tesis.aprobar_tutor', $inscripcion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de aprobar el tema de trabajo final de grado del alumno {{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</h4>
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
    @can('anular_tutor_inscripciones_tesis')
        <div class="modal fade flip" id="unapproveTutorModal" tabindex="-1" aria-labelledby="unapproveTutorModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Anular Aprobación de Tema de Trabajo Final de Grado</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form action="{{route('inscripciones_temas_tesis.anular_aprobacion_tutor', $inscripcion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>Está seguro de anular la aprobación del tutor del tema de trabajo final de grado del alumno {{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</h4>
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
