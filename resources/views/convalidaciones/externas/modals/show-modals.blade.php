@foreach ($convalidacion->convalidacionDetalles as $detalle)
    <!-- dictaminarModal -->
        @can('dictaminar_convalidaciones_externas')
            <div class="modal fade flip" id="dictaminarModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="dictaminarModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title">Dictaminar Convalidación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito.</p>
                            <form id="dictaminar-form-{{$detalle->id}}" action="{{route('convalidaciones_externas.dictaminar', $detalle->id)}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-8 mb-3">
                                        <label class="form-label" for="alumno">Alumno</label>
                                        <input type="text" class="form-control" id="alumno" value="{{$convalidacion->alumno->primer_nombre}} {{$convalidacion->alumno->primer_apellido}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="documento_alumno">N° Documento</label>
                                        <input type="text" class="form-control" id="documento_alumno" value="{{$convalidacion->alumno->numero_documento}}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="materia_origen">Materia Origen</label>
                                        <input type="text" class="form-control" id="materia_origen" value="{{$detalle->materia_origen}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="calificacion_origen">Calificación</label>
                                        <input type="text" class="form-control text-center" id="calificacion_origen" value="{{$detalle->calificacion_origen}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="carga_horaria_materia_origen-{{$detalle->id}}">Carga Horaria Origen <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center @error('carga_horaria_materia_origen') is-invalid @enderror" id="carga_horaria_materia_origen-{{$detalle->id}}" name="carga_horaria_materia_origen" value="{{old('carga_horaria_materia_origen', $detalle->carga_horaria_materia_origen)}}">
                                        @error('carga_horaria_materia_origen')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="numero_dictamen-{{$detalle->id}}">N° Dictamen <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control @error('numero_dictamen') is-invalid @enderror" id="numero_dictamen-{{$detalle->id}}" name="numero_dictamen" value="{{old('numero_dictamen', $detalle->numero_dictamen)}}">
                                        @error('numero_dictamen')
                                            <span class="invalid-feedback" role="aler">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="fecha_dictamen">Fecha Dictamen <span class="text-danger">(*)</span></label>
                                        @if ($detalle->fecha_dictamen)
                                            <input type="date" class="form-control text-center @error('fecha_dictamen') is-invalid @enderror" id="fecha_dictamen" name="fecha_dictamen" value="{{old('fecha_dictamen', $detalle->fecha_dictamen)}}">
                                        @else
                                            <input type="date" class="form-control text-center @error('fecha_dictamen') is-invalid @enderror" id="fecha_dictamen" name="fecha_dictamen" value="{{old('fecha_dictamen', $fecha_hoy)}}">
                                        @endif
                                        @error('fecha_dictamen')
                                            <span class="invalid-feedback" role="aler">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-8 mb-3">
                                        <label class="form-label" for="materia">Materia USL <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control @error('materia') is-invalid @enderror" id="materia-{{$detalle->id}}" name="materia" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($materias as $materia)
                                                <option value="{{$materia->id}}" @if (old('materia') == strval($materia->id) || $detalle->materia_id == strval($materia->id)) selected @endif>{{$materia->nombre_fantasia}}</option>
                                            @endforeach
                                        </select>
                                        @error('materia')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="porcentaje_coincidencia_bruta-{{$detalle->id}}">Coincidencia Bruta <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje_coincidencia_bruta @error('porcentaje_coincidencia_bruta') is-invalid @enderror" id="porcentaje_coincidencia_bruta-{{$detalle->id}}" name="porcentaje_coincidencia_bruta" value="{{old('porcentaje_coincidencia_bruta', number_format($detalle->porcentaje_coincidencia_bruta, 0, ',', '.'))}}">
                                            <span class="input-group-text porcentaje_span">%</span>
                                            @error('porcentaje_coincidencia_bruta')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-3" id="div-dictamen">
                                        @if ($detalle->ubicacion_dictamen)
                                            <div class="d-flex justify-content-center">
                                                <a type="button" class="btn btn-warning me-2" href="{{asset($detalle->ubicacion_dictamen)}}" target="_blank">Ver Adjunto</a>
                                                @can('eliminar_adjunto_dictamen_convalidaciones_externas')
                                                    <button type="button" class="btn btn-danger destroy-dictamen-btn" data-id="{{$detalle->id}}">Eliminar Adjunto</button>
                                                @endcan
                                            </div>
                                            <hr>
                                        @else
                                            <label class="form-label" for="dictamen">Adjuntar Dictamen</label>
                                            <div class="input-group custom-file-button">
                                                <input type="file" class="form-control dictamen @error('dictamen') is-invalid @enderror" id="dictamen-{{$detalle->id}}" name="dictamen" accept="image/jpeg,image/png,application/pdf" data-id="{{$detalle->id}}">
                                                <button type="button" class="btn btn-outline-danger eliminar-dictamen" id="eliminar-dictamen-{{$detalle->id}}" disabled data-id="{{$detalle->id}}"><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                                @error('dictamen')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <p class="text-muted" style="font-size: 14px">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code>, <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success dictaminar-btn" id="dictaminar-btn" data-id="{{$detalle->id}}" data-url="{{route('convalidaciones_externas.dictaminar', $detalle->id)}}">
                                            @if ($detalle->numero_dictamen)
                                                Actualizar
                                            @else
                                                Dictaminar
                                            @endif
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                            @can('eliminar_adjunto_dictamen_convalidaciones_externas')
                                <form action="{{route('convalidaciones_externas.destroy_dictamen', $detalle->id)}}" method="delete" id="destroy-dictamen-form-{{$detalle->id}}">
                                    @csrf
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /dictaminarModal -->

    <!-- aprobarModal -->
        @can('resolucion_convalidaciones_externas')
            <div class="modal fade flip" id="aprobarModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="aprobarModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title">Aprobar Convalidación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito.</p>
                            <form id="aprobar-form-{{$detalle->id}}" action="{{route('convalidaciones_externas.aprobar', $detalle->id)}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-8 mb-3">
                                        <label class="form-label" for="alumno">Alumno</label>
                                        <input type="text" class="form-control" id="alumno" value="{{$convalidacion->alumno->primer_nombre}} {{$convalidacion->alumno->primer_apellido}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="documento_alumno">N° Documento</label>
                                        <input type="text" class="form-control" id="documento_alumno" value="{{$convalidacion->alumno->numero_documento}}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="materia_origen">Materia Origen</label>
                                        <input type="text" class="form-control" id="materia_origen" value="{{$detalle->materia_origen}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="calificacion_origen">Calificación</label>
                                        <input type="text" class="form-control text-center" id="calificacion_origen" value="{{$detalle->calificacion_origen}}" readonly>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="carga_horaria_materia_origen">Carga Horaria Origen</label>
                                        <input type="text" class="form-control text-center" id="carga_horaria_materia_origen" value="{{$detalle->carga_horaria_materia_origen}}" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="numero_dictamen">N° Dictamen</label>
                                        <input type="text" class="form-control" id="numero_dictamen" value="{{$detalle->numero_dictamen}}" readonly>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="fecha_dictamen">Fecha Dictamen</label>
                                        <input type="text" class="form-control text-center @error('fecha_dictamen') is-invalid @enderror" id="fecha_dictamen" value="{{\Carbon\Carbon::parse($detalle->fecha_dictamen)->format('d/m/Y')}}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-8 mb-3">
                                        <label class="form-label" for="materia">Materia USL</label>
                                        <select class="form-control" id="materia" readonly>
                                            @foreach ($materias as $materia)
                                                @if ($detalle->materia_id == $materia->id)
                                                    <option value="{{$materia->id}}" selected>{{$materia->nombre_fantasia}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="porcentaje_coincidencia_bruta">Coincidencia Bruta</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center" id="porcentaje_coincidencia_bruta-{{$detalle->id}}" value="{{number_format($detalle->porcentaje_coincidencia_bruta, 0, ',', '.')}}" readonly>
                                            <span class="input-group-text porcentaje_span">%</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="numero_resolucion">N° Resolución <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control @error('numero_resolucion') is-invalid @enderror" id="numero_resolucion-{{$detalle->id}}" name="numero_resolucion" value="{{old('numero_resolucion', $detalle->numero_resolucion)}}">
                                        @error('numero_resolucion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="fecha_resolucion">Fecha Resolución <span class="text-danger">(*)</span></label>
                                        @if ($detalle->fecha_resolucion)
                                            <input type="date" class="form-control text-center @error('fecha_resolucion') is-invalid @enderror" id="fecha_resolucion" name="fecha_resolucion" value="{{old('fecha_resolucion', $detalle->fecha_resolucion)}}">
                                        @else
                                            <input type="date" class="form-control text-center @error('fecha_resolucion') is-invalid @enderror" id="fecha_resolucion" name="fecha_resolucion" value="{{old('fecha_resolucion', $fecha_hoy)}}">
                                        @endif
                                        @error('fecha_resolucion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-3" id="div-resolucion">
                                        @if ($detalle->ubicacion_resolucion)
                                            <div class="d-flex justify-content-center">
                                                <a type="button" class="btn btn-warning me-2" href="{{asset($detalle->ubicacion_resolucion)}}" target="_blank">Ver Adjunto</a>
                                                @can('eliminar_adjunto_resolucion_convalidaciones_externas')
                                                    <button type="button" class="btn btn-danger destroy-resolucion-btn" data-id="{{$detalle->id}}">Eliminar Adjunto</button>
                                                @endcan
                                            </div>
                                            <hr>
                                        @else
                                            <label class="form-label" for="resolucion">Adjuntar Resolución</label>
                                            <div class="input-group custom-file-button">
                                                <input type="file" class="form-control resolucion @error('resolucion') is-invalid @enderror" id="resolucion-{{$detalle->id}}" name="resolucion" accept="image/jpeg,image/png,application/pdf" data-id="{{$detalle->id}}">
                                                <button type="button" class="btn btn-outline-danger eliminar-resolucion" id="eliminar-resolucion-{{$detalle->id}}" disabled data-id="{{$detalle->id}}"><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                                @error('resolucion')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <p class="text-muted" style="font-size: 14px">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code>, <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="button" class="btn btn-success aprobar-btn" id="aprobar-btn" data-id="{{$detalle->id}}" data-url="{{route('convalidaciones_externas.aprobar', $detalle->id)}}">
                                            @if ($detalle->numero_resolucion)
                                                Actualizar
                                            @else
                                                Aprobar
                                            @endif
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                            @can('eliminar_adjunto_resolucion_convalidaciones_externas')
                                <form action="{{route('convalidaciones_externas.destroy_resolucion', $detalle->id)}}" method="delete" id="destroy-resolucion-form-{{$detalle->id}}">
                                    @csrf
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /aprobarModal -->

    <!-- showModal -->
        <div class="modal fade flip" id="showModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="showModal-{{$detalle->id}}" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Ver Convalidación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-lg-8 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$convalidacion->alumno->primer_nombre}} {{$convalidacion->alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="documento_alumno">N° Documento</label>
                                    <input type="text" class="form-control" id="documento_alumno" value="{{$convalidacion->alumno->numero_documento}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="materia_origen">Materia Origen</label>
                                    <input type="text" class="form-control" id="materia_origen" value="{{$detalle->materia_origen}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="calificacion_origen">Calificación</label>
                                    <input type="text" class="form-control text-center" id="calificacion_origen" value="{{$detalle->calificacion_origen}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="carga_horaria_materia_origen">Carga Horaria Origen</label>
                                    <input type="text" class="form-control text-center" id="carga_horaria_materia_origen" value="{{$detalle->carga_horaria_materia_origen}}" readonly>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="numero_dictamen">N° Dictamen</label>
                                    <input type="text" class="form-control" id="numero_dictamen" value="{{$detalle->numero_dictamen}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha_dictamen">Fecha Dictamen</label>
                                    @if ($detalle->fecha_dictamen)
                                        <input type="text" class="form-control text-center" id="fecha_dictamen" value="{{\Carbon\Carbon::parse($detalle->fecha_dictamen)->format('d/m/Y')}}" readonly>
                                    @else
                                        <input type="text" class="form-control text-center" id="fecha_dictamen" value="" readonly>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8 mb-3">
                                    <label class="form-label" for="materia">Materia USL</label>
                                    <select class="form-control" id="materia" readonly>
                                        @foreach ($materias as $materia)
                                            @if ($detalle->materia_id == $materia->id)
                                                <option value="{{$materia->id}}" selected>{{$materia->nombre_fantasia}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="porcentaje_coincidencia_bruta">Coincidencia Bruta</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center" id="porcentaje_coincidencia_bruta-{{$detalle->id}}" value="{{number_format($detalle->porcentaje_coincidencia_bruta, 0, ',', '.')}}" readonly>
                                        <span class="input-group-text porcentaje_span">%</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="numero_resolucion">N° Resolución</label>
                                    <input type="text" class="form-control" id="numero_resolucion" value="{{$detalle->numero_resolucion}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha_resolucion">Fecha Resolución</label>
                                    @if ($detalle->fecha_resolucion)
                                        <input type="text" class="form-control text-center" id="fecha_resolucion" value="{{\Carbon\Carbon::parse($detalle->fecha_resolucion)->format('d/m/Y')}}" readonly>
                                    @else
                                        <input type="text" class="form-control text-center" id="fecha_resolucion" value="" readonly>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3 d-flex justify-content-center">
                                    @if ($detalle->ubicacion_dictamen)
                                        <a type="button" class="btn btn-info me-2" href="{{asset($detalle->ubicacion_dictamen)}}" target="_blank">Ver Dictamen</a>
                                    @endif
                                    @if ($detalle->ubicacion_resolucion)
                                        <a type="button" class="btn btn-success me-2" href="{{asset($detalle->ubicacion_resolucion)}}" target="_blank">Ver Resolución</a>
                                    @endif
                                </div>
                                @if ($detalle->ubicacion_dictamen || $detalle->ubicacion_resolucion)
                                    <hr>
                                @endif
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <!-- /showModal -->
@endforeach
