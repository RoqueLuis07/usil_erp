@foreach ($materias as $materia)
    <!-- subirPlanClaseModal -->
        @can('adjuntar_planes_clases_docentes_pantalla')
            <div class="modal fade flip" id="subirPlanClaseModal-{{$materia->id}}" tabindex="-1" aria-labelledby="subirPlanClaseModal-{{$materia->id}}" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title">Subir Plan de Clases</h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('planes_programas_clases_docentes.store_planes_clases', $materia->id)}}" method="post" id="store-plan-clase-form-{{$materia->id}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="materia" class="form-label">Materia</label>
                                            <input type="text" class="form-control materia-{{$materia->id}}" value="{{$materia->materia->nombre_fantasia}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="form-label" for="plan">Plan de Clases <span class="text-danger">(*)</span></label>
                                    <div class="col-lg-3 mt-3" id="div-copiar-plan-{{$materia->id}}">
                                        <button type="button" class="btn btn-outline-primary copy-btn" data-id="{{$materia->id}}" data-tipo="Plan"><i class="ri-file-copy-2-fill align-bottom me-2"></i>Copiar Anterior</button>
                                    </div>
                                    <div class="col-lg-9 mt-3" id="div-plan-{{$materia->id}}">
                                        <div class="input-group custom-file-button">
                                            <input type="file" class="form-control @error('plan') is-invalid @enderror adjunto" id="plan-{{$materia->id}}" name="adjunto" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);">
                                            <button type="button" class="btn btn-outline-danger eliminar-adjunto" data-id="{{$materia->id}}" data-tipo="Plan" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                            @error('plan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <p class="text-muted">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success save-adjunto-btn" data-id="{{$materia->id}}" data-tipo="Plan" data-url="{{route('planes_programas_clases_docentes.store_planes_clases', $materia->id)}}">Subir</button>
                                        <button type="button" class="btn btn-danger cancel-adjunto-btn" data-id="{{$materia->id}}" data-tipo="Plan">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                            <form action="{{route('planes_programas_clases_docentes.copy_planes_clases', $materia->id)}}" method="post" id="copy-plan-clase-form-{{$materia->id}}">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /subirPlanClaseModal -->

    <!-- subirProgramaClaseModal -->
        @can('adjuntar_programas_clases_docentes_pantalla')
            <div class="modal fade flip" id="subirProgramaClaseModal-{{$materia->id}}" tabindex="-1" aria-labelledby="subirProgramaClaseModal-{{$materia->id}}" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title">Subir Programa de Estudio</h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('planes_programas_clases_docentes.store_programas_clases', $materia->id)}}" method="post" id="store-programa-clase-form-{{$materia->id}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="materia" class="form-label">Materia</label>
                                            <input type="text" class="form-control materia-{{$materia->id}}" value="{{$materia->materia->nombre_fantasia}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="form-label" for="programa">Programa de Estudio <span class="text-danger">(*)</span></label>
                                    <div class="col-lg-3 mt-3" id="div-copiar-programa-{{$materia->id}}">
                                        <button type="button" class="btn btn-outline-primary copy-btn" data-id="{{$materia->id}}" data-tipo="Programa"><i class="ri-file-copy-2-fill align-bottom me-2"></i>Copiar Anterior</button>
                                    </div>
                                    <div class="col-lg-9 mt-3" id="div-programa-{{$materia->id}}">
                                        <div class="input-group custom-file-button">
                                            <input type="file" class="form-control @error('programa') is-invalid @enderror adjunto" id="programa-{{$materia->id}}" name="adjunto" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);">
                                            <button type="button" class="btn btn-outline-danger eliminar-adjunto" data-id="{{$materia->id}}" data-tipo="Programa" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                            @error('programa')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <p class="text-muted">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success save-adjunto-btn" data-id="{{$materia->id}}" data-tipo="Programa" data-url="{{route('planes_programas_clases_docentes.store_programas_clases', $materia->id)}}">Subir</button>
                                        <button type="button" class="btn btn-danger cancel-adjunto-btn" data-id="{{$materia->id}}" data-tipo="Programa">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                            <form action="{{route('planes_programas_clases_docentes.copy_programas_clases', $materia->id)}}" method="post" id="copy-programa-clase-form-{{$materia->id}}">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /subirProgramaClaseModal -->
@endforeach

