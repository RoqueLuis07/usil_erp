<!-- generateClaseModal -->
    @can('crear_clases_docentes_pantalla')
        <div class="modal fade flip" id="generateClaseModal" tabindex="-1" aria-labelledby="generateClaseModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="generateClaseModal">Nueva Clase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="usuario_clase" name="usuario_clase" value="{{Auth::id()}}">
                            <input type="hidden" id="periodo_activo_clase" name="periodo_activo_clase" value="{{$periodo_activo}}">
                            <div class="col-lg-12 mb-3">
                                <label class="form-label" for="materia_clase">Materia <span class="text-danger">(*)</span></label>
                                <select class="form-control selectpicker" id="materia_clase" name="materia_clase" data-live-search="true">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    @foreach ($materias as $materia)
                                        <option value="{{$materia->materia_id}}">{{$materia->materia->nombre_fantasia}}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" role="alert">
                                    <strong>Seleccione una materia antes de continuar.</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-info" id="btn-generate-clases">Ir a Cargar</a>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /generateClaseModal -->

<!-- chargeAsistenciaModal -->
    @can('crear_asistencias_docentes_pantalla')
        <div class="modal fade flip" id="chargeAsistenciaModal" tabindex="-1" aria-labelledby="chargeAsistenciaModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="chargeAsistenciaModal">Cargar Asistencias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="usuario_asistencia" name="usuario_asistencia" value="{{Auth::id()}}">
                            <input type="hidden" id="periodo_activo_asistencia" name="periodo_activo_asistencia" value="{{$periodo_activo}}">
                            <div class="col-lg-12 mb-3">
                                <label class="form-label" for="materia_asistencia">Materia <span class="text-danger">(*)</span></label>
                                <select class="form-control selectpicker" id="materia_asistencia" name="materia_asistencia" data-live-search="true">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    @foreach ($materias as $materia)
                                        <option value="{{$materia->materia_id}}">{{$materia->materia->nombre_fantasia}}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" role="alert">
                                    <strong>Seleccione una materia antes de continuar.</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-info" id="btn-charge-asistencias">Ir a Cargar</a>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /chargeAsistenciaModal -->

<!-- chargeEvaluacionModal -->
    @can('crear_evaluaciones_docentes_pantalla')
        <div class="modal fade flip" id="chargeEvaluacionModal" tabindex="-1" aria-labelledby="chargeEvaluacionModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="chargeEvaluacionModal">Nueva Evaluación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="usuario_evaluacion" name="usuario_evaluacion" value="{{Auth::id()}}">
                            <input type="hidden" id="periodo_activo_evaluacion" name="periodo_activo_evaluacion" value="{{$periodo_activo}}">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="materia_evaluacion">Materia <span class="text-danger">(*)</span></label>
                                <select class="form-control selectpicker" id="materia_evaluacion" name="materia_evaluacion" data-live-search="true">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    @foreach ($materias as $materia)
                                        <option value="{{$materia->materia_id}}">{{$materia->materia->nombre_fantasia}}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" role="alert">
                                    <strong>Seleccione una materia antes de continuar.</strong>
                                </span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="carrera_evaluacion">Carrera <span class="text-danger">(*)</span></label>
                                <select class="form-control selectpicker" id="carrera_evaluacion" name="carrera_evaluacion" data-live-search="true">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    @foreach ($carreras as $carrera)
                                        <option value="{{$carrera->id}}" data-subtext="{{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" role="alert">
                                    <strong>Seleccione una carrera antes de continuar.</strong>
                                </span>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tipo_evaluacion">Tipo Evaluación <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker" id="tipo_evaluacion" name="tipo_evaluacion" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    <span class="invalid-feedback" role="alert">
                                        <strong>Seleccione un tipo de evaluación antes de continuar.</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-info" id="btn-charge-evaluaciones">Ir a Generar</a>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /chargeEvaluacionModal -->
