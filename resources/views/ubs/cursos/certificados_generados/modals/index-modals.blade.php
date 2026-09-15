@foreach ($certificados as $certificado)
    <!-- regenerateCertificadoModal -->
        @can('regenerar_certificados_cursos_ubs')
            <div class="modal fade flip" id="regenerateCertificadoModal-{{$certificado->id}}" tabindex="-1" aria-labelledby="regenerateCertificadoModal-{{$certificado->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de generar el certificado de {{$certificado->alumno->primer_nombre}} {{$certificado->alumno->primer_apellido}} en el curso {{$certificado->curso->nombre_fantasia}}?</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <a type="button" class="btn btn-warning" href="{{route('cursos.regenerate_certificados', $certificado->id)}}" target="_blank">Sí, generar!</a>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /regenerateCertificadoModal -->

    <!-- editCertificadoModal -->
        @can('editar_certificados_cursos_ubs')
            <div class="modal fade flip" id="editCertificadoModal-{{$certificado->id}}" tabindex="-1" aria-labelledby="editCertificadoModal-{{$certificado->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h4 class="modal-title">Editar Certificado</h4>
                        </div>
                        <div class="modal-body">
                            <form id="update-form-{{$certificado->id}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="curso-{{$certificado->id}}">Curso</label>
                                        <input type="text" class="form-control" id="curso-{{$certificado->id}}" value="{{$certificado->curso->nombre_fantasia}}" readonly>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="alumno-{{$certificado->id}}">Alumno</label>
                                        <input type="text" class="form-control" id="alumno-{{$certificado->id}}" value="{{$certificado->alumno->primer_nombre}} {{$certificado->alumno->primer_apellido}}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="numero_orden-{{$certificado->id}}">N° de Orden <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center @error('numero_orden') is-invalid @enderror" id="numero_orden-{{$certificado->id}}" name="numero_orden" value="{{old('numero_orden', $certificado->numero_orden)}}">
                                        @error('numero_orden')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="numero_pagina-{{$certificado->id}}">N° de Página <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center @error('numero_pagina') is-invalid @enderror" id="numero_pagina-{{$certificado->id}}" name="numero_pagina" value="{{old('numero_pagina', $certificado->numero_pagina)}}">
                                        @error('numero_pagina')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success update-btn" data-id="{{$certificado->id}}" data-url="{{route('cursos.update_certificados_generados', $certificado->id)}}">Actualizar</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /editCertificadoModal -->
@endforeach
