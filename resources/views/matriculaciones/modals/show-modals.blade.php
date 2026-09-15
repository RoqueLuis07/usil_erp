<!-- agregarConvenioModal -->
    @can('agregar_matriculaciones_convenios')
        <div class="modal fade flip" id="agregarConvenioModal" tabindex="-1" aria-labelledby="agregarConvenioModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="agregar-convenio-form" method="post" action="{{ route('matriculaciones.agregar_convenio', $matriculacion->id) }}">
                    @csrf
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="agregarConvenioModal">Vincular Convenio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="convenio">Convenio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('convenio') is-invalid @enderror" id="convenio" name="convenio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($convenios as $convenio)
                                            <option value="{{ $convenio->id }}" @if (old('convenio') == strval($convenio->id)) selected @endif>{{ $convenio->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('convenio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn" data-url="{{ route('matriculaciones.agregar_convenio', $matriculacion->id) }}">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
<!-- /agregarConvenioModal -->

<!-- eliminarConvenioModal -->
    @can('eliminar_matriculaciones_convenios')
        <div class="modal fade flip" id="eliminarConvenioModal" tabindex="-1" aria-labelledby="eliminarConvenioModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="eliminar-convenio-form" action="{{route('matriculaciones.eliminar_convenio', $matriculacion->id)}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de desvincular el convenio de la matriculación del alumno {{$matriculacion->alumno->primer_nombre}} {{$matriculacion->alumno->primer_apellido}} en la carrera {{$matriculacion->carrera->nombre_fantasia}} en el semestre {{$matriculacion->semestre->nombre}}?</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, desvincular!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /eliminarConvenioModal -->

@if ($matriculacion->convenio_id)
    <!-- showConvenioModal -->
    @can('ver_matriculaciones_convenios')
        <div class="modal fade flip" id="showConvenioModal" tabindex="-1" aria-labelledby="showConvenioModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form>
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="showConvenioModal">Visualizar Convenio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="convenio">Convenio</label>
                                    <input type="text" class="form-control" id="convenio" value="{{ $matriculacion->convenio->nombre }}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <label class="form-label" for="detalles_convenio">Detalles del Convenio</label>
                                <hr>
                                <div class="row">
                                    <h6 class="text-center">Matrícula</h6>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="tipo_matricula">Tipo</label>
                                        <input type="text" class="form-control text-center" id="tipo_matricula" @if ($matriculacion->convenio->detalle->tipo_matricula == 'VA') value="VALOR FIJO" @elseif($matriculacion->convenio->detalle->tipo_matricula == 'PO') value="PORCENTAJE" @endif readonly>
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="valor_matricula">@if ($matriculacion->convenio->detalle->tipo_matricula == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_matricula == 'PO') Porcentaje @endif</label>
                                        <div class="input-group">
                                            @if ($matriculacion->convenio->detalle->tipo_matricula == 'VA')
                                                <span class="input-group-text">Gs.</span>
                                            @endif
                                            <input type="text" class="form-control text-center" id="valor_matricula" @if ($matriculacion->convenio->detalle->tipo_matricula == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_matricula }}" @elseif($matriculacion->convenio->detalle->tipo_matricula == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_matricula }}" @endif readonly>
                                            @if ($matriculacion->convenio->detalle->tipo_matricula == 'PO')
                                                <span class="input-group-text">%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($matriculacion->convenio->detalle->tipo_contado || $matriculacion->convenio->detalle->tipo_cuotas)
                                    <hr>
                                @endif
                                @if ($matriculacion->convenio->detalle->tipo_contado)
                                    <div class="row">
                                        <h6 class="text-center">Contado</h6>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="tipo_contado">Tipo</label>
                                            <input type="text" class="form-control text-center" id="tipo_contado" @if ($matriculacion->convenio->detalle->tipo_contado == 'VA') value="VALOR FIJO" @elseif($matriculacion->convenio->detalle->tipo_contado == 'PO') value="PORCENTAJE" @endif readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="valor_contado">@if ($matriculacion->convenio->detalle->tipo_contado == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_contado == 'PO') Porcentaje @endif</label>
                                            <div class="input-group">
                                                @if ($matriculacion->convenio->detalle->tipo_contado == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="valor_contado" @if ($matriculacion->convenio->detalle->tipo_contado == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_contado }}" @elseif($matriculacion->convenio->detalle->tipo_contado == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_contado }}" @endif readonly>
                                                @if ($matriculacion->convenio->detalle->tipo_contado == 'PO')
                                                    <span class="input-group-text">%</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endif
                                @if ($matriculacion->convenio->detalle->tipo_cuotas)
                                    <div class="row">
                                        <h6 class="text-center">Cuotas</h6>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="tipo_cuotas">Tipo</label>
                                            <input type="text" class="form-control text-center" id="tipo_cuotas" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="VALOR FIJO" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="PORCENTAJE" @endif readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="aplica_a_cuotas">Aplica a</label>
                                            <input type="text" class="form-control text-center" id="aplica_a_cuotas" @if ($matriculacion->convenio->detalle->aplica_a_cuotas == '1C') value="PRIMERA CUOTA" @elseif($matriculacion->convenio->detalle->aplica_a_cuotas == 'CO') value="COMBINADO" @elseif($matriculacion->convenio->detalle->aplica_a_cuotas == 'TO') value="TODAS LAS CUOTAS" @endif readonly>
                                            @error('aplica_a_cuotas')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        @if ($matriculacion->convenio->detalle->aplica_a_cuotas == 'TO')
                                            <div class="col-lg-6 mb-3 text-center">
                                                <label class="form-label" for="descuento_cuotas">@if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif</label>
                                                <div class="input-group">
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA')
                                                        <span class="input-group-text">Gs.</span>
                                                    @endif
                                                    <input type="text" class="form-control text-center" id="descuento_cuotas" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_cuotas }}" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_cuotas }}" @endif readonly>
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'PO')
                                                        <span class="input-group-text">%</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        @if ($matriculacion->convenio->detalle->aplica_a_cuotas == '1C' || $matriculacion->convenio->detalle->aplica_a_cuotas == 'CO')
                                            <div class="col-lg-6 mb-3 text-center">
                                                <label class="form-label" for="descuento_cuota_1">@if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 1</label>
                                                <div class="input-group">
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA')
                                                        <span class="input-group-text">Gs.</span>
                                                    @endif
                                                    <input type="text" class="form-control text-center" id="descuento_cuota_1" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_cuota_1 }}" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_cuota_1 }}" @endif readonly>
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'PO')
                                                        <span class="input-group-text">%</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if ($matriculacion->convenio->detalle->aplica_a_cuotas == 'CO')
                                            <div class="col-lg-6 mb-3 text-center">
                                                <label class="form-label" for="descuento_cuota_2">@if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 2</label>
                                                <div class="input-group">
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA')
                                                        <span class="input-group-text">Gs.</span>
                                                    @endif
                                                    <input type="text" class="form-control text-center" id="descuento_cuota_2" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_cuota_2 }}" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_cuota_2 }}" @endif readonly>
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'PO')
                                                        <span class="input-group-text">%</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3 text-center">
                                                <label class="form-label" for="descuento_cuota_3">@if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 3</label>
                                                <div class="input-group">
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA')
                                                        <span class="input-group-text">Gs.</span>
                                                    @endif
                                                    <input type="text" class="form-control text-center" id="descuento_cuota_3" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_cuota_3 }}" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_cuota_3 }}" @endif readonly>
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'PO')
                                                        <span class="input-group-text">%</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3 text-center">
                                                <label class="form-label" for="descuento_cuota_4">@if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 4</label>
                                                <div class="input-group">
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA')
                                                        <span class="input-group-text">Gs.</span>
                                                    @endif
                                                    <input type="text" class="form-control text-center" id="descuento_cuota_4" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_cuota_4 }}" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_cuota_4 }}" @endif readonly>
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'PO')
                                                        <span class="input-group-text">%</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3 text-center">
                                                <label class="form-label" for="descuento_cuota_5">@if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 5</label>
                                                <div class="input-group">
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA')
                                                        <span class="input-group-text">Gs.</span>
                                                    @endif
                                                    <input type="text" class="form-control text-center" id="descuento_cuota_5" @if ($matriculacion->convenio->detalle->tipo_cuotas == 'VA') value="{{ $matriculacion->convenio->detalle->descuento_cuota_5 }}" @elseif($matriculacion->convenio->detalle->tipo_cuotas == 'PO') value="{{ $matriculacion->convenio->detalle->porcentaje_cuota_5 }}" @endif readonly>
                                                    @if ($matriculacion->convenio->detalle->tipo_cuotas == 'PO')
                                                        <span class="input-group-text">%</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <!-- /showConvenioModal -->
@endif
