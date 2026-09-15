@foreach ($semestre_malla->semestreMallaMaterias as $detalle)
    <!-- editSemestreMallaMateriaModal -->
        @can('editar_parametros_materias_periodos')
            <div class="modal fade flip" id="editSemestreMallaMateriaModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="editSemestreMallaMateriaModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-hedaer bg-light p-3">
                            <h5 class="modal-title" id="title-materia-{{$detalle->id}}">{{$detalle->materia->nombre_fantasia}} <small class="text-muted">{{$semestre_malla->malla->tipoMalla->nombre}}</small></h5>
                        </div>
                        <div class="modal-body">
                            <form id="update-semestre-malla-materia-form-{{$detalle->id}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="docente">Docente @if ($detalle->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label>
                                        @if ($detalle->semestreMalla->semestre->estado == 'IN')
                                            <input type="text" class="form-control" id="docente" @if ($detalle->docente_id) value="{{$detalle->docente->primer_nombre}} {{$detalle->docente->primer_apellido}}" @endif readonly>
                                        @else
                                            <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true" tabindex="0">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($docentes as $docente)
                                                    <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id) || $detalle->docente_id == strval($docente->id)) selected @endif>{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                                @endforeach
                                            </select>
                                            @error('docente')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        @endif
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="aula">Aula @if ($detalle->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label>
                                        <input type="text" class="form-control text-center @error('aula') is-invalid @enderror" id="aula" name="aula" value="{{old('aula', $detalle->aula)}}" @if ($detalle->semestreMalla->semestre->estado == 'IN') readonly @endif>
                                        @error('aula')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_examen_parcial">Fecha de Parcial @if ($detalle->semestreMalla->semestre->estado != 'IN' || ($detalle->semestreMalla->malla->carrera->programa_id != 3 && $detalle->semestreMalla->semestre->estado != 'IN')) <span class="text-danger">(*)</span> @endif</label>
                                        <input type="date" class="form-control text-center @error('fecha_examen_parcial') is-invalid @enderror" id="fecha_examen_parcial" name="fecha_examen_parcial" value="{{old('fecha_examen_parcial', $detalle->fecha_examen_parcial)}}" @if ($detalle->semestreMalla->semestre->estado == 'IN') readonly @endif>
                                        @error('fecha_examen_parcial')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_examen_ordinario">Fecha de Final @if ($detalle->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label>
                                        <input type="date" class="form-control text-center @error('fecha_examen_ordinario') is-invalid @enderror" id="fecha_examen_ordinario" name="fecha_examen_ordinario" value="{{old('fecha_examen_ordinario', $detalle->fecha_examen_ordinario)}}" @if ($detalle->semestreMalla->semestre->estado == 'IN') readonly @endif>
                                        @error('fecha_examen_ordinario')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_examen_complementario">Fecha de Complementario @if ($detalle->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label>
                                        <input type="date" class="form-control text-center @error('fecha_examen_complementario') is-invalid @enderror" id="fecha_examen_complementario" name="fecha_examen_complementario" value="{{old('fecha_examen_complementario', $detalle->fecha_examen_complementario)}}" @if ($detalle->semestreMalla->semestre->estado == 'IN') readonly @endif>
                                        @error('fecha_examen_complementario')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_examen_extraordinario">Fecha de Extraordinario @if ($detalle->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label>
                                        <input type="date" class="form-control text-center @error('fecha_examen_extraordinario') is-invalid @enderror" id="fecha_examen_extraordinario" name="fecha_examen_extraordinario" value="{{old('fecha_examen_extraordinario', $detalle->fecha_examen_extraordinario)}}" @if ($detalle->semestreMalla->semestre->estado == 'IN') readonly @endif>
                                        @error('fecha_examen_extraordinario')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <label class="form-label" for="observaciones">Observaciones</label>
                                        <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" cols="30" rows="3" @if ($detalle->semestreMalla->semestre->estado == 'IN') readonly @endif>{{old('observaciones', $detalle->observaciones)}}</textarea>
                                        @error('observaciones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success update-semestre-malla-materia-btn @if ($detalle->semestreMalla->semestre->estado == 'IN') d-none @endif" data-id="{{$detalle->id}}" data-url="{{route('semestres_mallas_materias.update', $detalle->id)}}">Actualizar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /editSemestreMallaMateriaModal -->
@endforeach
