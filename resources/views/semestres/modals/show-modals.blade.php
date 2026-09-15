@foreach ($semestre->semestreMallas as $detalle)
    <!-- editSemestreMallaModal -->
        @can('editar_parametros_carreras_periodos')
            <div class="modal fade flip" id="editSemestreMallaModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="editSemestreMallaModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-hedaer bg-light p-3">
                            <h5 class="modal-title" id="title-malla-{{$detalle->id}}">{{$detalle->malla->carrera->nombre_fantasia}} <small class="text-muted">{{$detalle->malla->carrera->nombre_real}} - {{$detalle->malla->tipoMalla->nombre}}</small></h5>
                        </div>
                        <div class="modal-body">
                            <form id="update-semestre-malla-form-{{$detalle->id}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 mb-3 text-center">
                                        <label class="form-label" for="coordinador">Coordinador <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control @error('coordinador') is-invalid @enderror" id="coordinador" name="coordinador" data-live-search="true" tabindex="0">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($coordinadores as $coordinador)
                                                <option value="{{$coordinador->id}}" @if (old('coordinador') == strval($coordinador->id) || $detalle->coordinador_id == strval($coordinador->id)) selected @endif>{{$coordinador->primer_nombre}} {{$coordinador->primer_apellido}}</option>
                                            @endforeach
                                        </select>
                                        @error('coordinador')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-3 text-center">
                                        <label class="form-label" for="fecha_inicio_matriculacion">Inicio Matriculación <span class="text-danger">(*)</span></label>
                                        <input type="date" class="form-control text-center form-control-icon @error('fecha_inicio_matriculacion') is-invalid @enderror" id="fecha_inicio_matriculacion" name="fecha_inicio_matriculacion" value="{{old('fecha_inicio_matriculacion', $detalle->fecha_inicio_matriculacion)}}">
                                        @error('fecha_inicio_matriculacion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-3 text-center">
                                        <label class="form-label" for="fecha_fin_matriculacion">Fin Matriculación <span class="text-danger">(*)</span></label>
                                        <input type="date" class="form-control text-center form-control-icon @error('fecha_fin_matriculacion') is-invalid @enderror" id="fecha_fin_matriculacion" name="fecha_fin_matriculacion" value="{{old('fecha_fin_matriculacion', $detalle->fecha_fin_matriculacion)}}">
                                        @error('fecha_fin_matriculacion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success update-semestre-malla-btn" data-id="{{$detalle->id}}" data-url="{{route('semestres_mallas.update', $detalle->id)}}">Actualizar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /editSemestreMallaModal -->
@endforeach
