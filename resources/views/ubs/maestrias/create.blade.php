@can('crear_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Agregar Maestría @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Maestrías @endslot
            @slot('title') Agregar Maestría  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('maestrias.store')}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva maestría</h4>
                            <input type="hidden" name="maestria" value="SI">
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="nombre_fantasia">Nombre Fantasía <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control @error('nombre_fantasia') is-invalid @enderror" id="nombre_fantasia" name="nombre_fantasia" placeholder="Escriba un nombre de fantasía" value="{{old('nombre_fantasia')}}">
                                                @error('nombre_fantasia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="nombre_real">Nombre Real <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control @error('nombre_real') is-invalid @enderror" id="nombre_real" name="nombre_real" placeholder="Escriba un nombre real" value="{{old('nombre_real')}}">
                                                @error('nombre_real')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="codigo">Código <span class="text-danger">(*)</label>
                                                <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" placeholder="Escriba una codigo" value="{{old('codigo')}}">
                                                @error('codigo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="programa">Programa <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control @error('programa') is-invalid @enderror" id="programa" name="programa" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($programas as $programa)
                                                        <option value="{{$programa->id}}" @if(old('programa') == strval($programa->id)) selected @endif>{{$programa->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('programa')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="facultad">Facultad <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control @error('facultad') is-invalid @enderror" id="facultad" name="facultad" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($facultades as $facultad)
                                                        <option value="{{$facultad->id}}" @if(old('facultad') == strval($facultad->id)) selected @endif>{{$facultad->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('facultad')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="tipo_curso">Tipo <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control @error('tipo_curso') is-invalid @enderror" id="tipo_curso" name="tipo_curso" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($tipos_maestrias as $tipo_curso)
                                                        <option value="{{$tipo_curso->id}}" @if(old('tipo_curso') == strval($tipo_curso->id)) selected @endif>{{$tipo_curso->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('tipo_curso')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="modalidad">Modalidad <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($modalidades as $modalidad)
                                                        <option value="{{$modalidad->id}}" @if(old('modalidad') == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('modalidad')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="fecha_apertura">Fecha de Apertura <span class="text-danger">(*)</span></label>
                                                <div class="form-icon right">
                                                    <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_apertura') is-invalid @enderror" id="fecha_apertura" name="fecha_apertura" value="{{old('fecha_apertura')}}" placeholder="Seleccionar...">
                                                    <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                                    @error('fecha_apertura')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="fecha_fin">Fecha de Fin <span class="text-danger">(*)</span></label>
                                                <div class="form-icon right">
                                                    <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin')}}" placeholder="Seleccionar...">
                                                    <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                                    @error('fecha_fin')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="cantidad_horas">Cantidad de Horas <span class="text-danger">(*)</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-center @error('cantidad_horas') is-invalid @enderror" id="cantidad_horas" name="cantidad_horas" value="{{old('cantidad_horas')}}" placeholder="60">
                                                    <span class="input-group-text">horas</span>
                                                    @error('cantidad_horas')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="duracion">Duración <span class="text-danger">(*)</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-center @error('duracion') is-invalid @enderror" id="duracion" name="duracion" value="{{old('duracion')}}" placeholder="60">
                                                    <span class="input-group-text">años</span>
                                                    @error('duracion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="cantidad_creditos">Cantidad de Créditos <span class="text-danger">(*)</label>
                                                <input type="text" class="form-control text-center @error('cantidad_creditos') is-invalid @enderror" id="cantidad_creditos" name="cantidad_creditos" placeholder="35" value="{{old('cantidad_creditos')}}">
                                                @error('cantidad_creditos')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="llamado">N° de Llamado <span class="text-danger">(*)</label>
                                                <input type="text" class="form-control text-center @error('llamado') is-invalid @enderror" id="llamado" name="llamado" placeholder="1" value="{{old('llamado')}}">
                                                @error('llamado')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="numero_ley">N° de Ley <span class="text-danger">(*)</label>
                                                <input type="text" class="form-control @error('numero_ley') is-invalid @enderror" id="numero_ley" name="numero_ley" value="{{old('numero_ley')}}">
                                                @error('numero_ley')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="numero_acta">N° de Acta <span class="text-danger">(*)</label>
                                                <input type="text" class="form-control @error('numero_acta') is-invalid @enderror" id="numero_acta" name="numero_acta" value="{{old('numero_acta')}}">
                                                @error('numero_acta')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="numero_resolucion_cones">N° de Res. del CONES <span class="text-danger">(*)</label>
                                                <input type="text" class="form-control @error('numero_resolucion_cones') is-invalid @enderror" id="numero_resolucion_cones" name="numero_resolucion_cones" value="{{old('numero_resolucion_cones')}}">
                                                @error('numero_resolucion_cones')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label" for="evaluacion">Tiene Evaluación ? <span class="text-danger">(*)</span></label>
                                                <div class="text-center">
                                                    <div class="btn-group @error('evaluacion') is-invalid @enderror" role="group">
                                                        <input type="radio" class="btn-check" id="evaluacion1" name="evaluacion" value="false" @if (old('evaluacion') == 'false') checked @endif>
                                                        <label class="btn btn-outline-danger" for="evaluacion1">No</label>
                                                        <input type="radio" class="btn-check" id="evaluacion2" name="evaluacion" value="true" @if (old('evaluacion') == 'true' || old('evaluacion') == null) checked @endif>
                                                        <label class="btn btn-outline-success" for="evaluacion2">Sí</label>
                                                    </div>
                                                    @error('evaluacion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <label class="form-label" for="div-cronograma">Cronograma</label>
                                            <div class="col-lg-12" id="div-cronograma">
                                                <div class="input-group custom-file-button">
                                                    <input type="file" class="form-control @error('cronograma') is-invalid @enderror" id="cronograma" name="cronograma" accept="image/jpeg,image/png" onchange="readURL(this);">
                                                    <button type="button" class="btn btn-outline-danger" id="eliminar-cronograma" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                                    @error('cronograma')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <p class="text-muted">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                        </div>
                                        <hr>
                                        <div class="row text-center">
                                            <label class="form-label" for="vista-imagen">Visualización Previa</label>
                                            <div class="text-center">
                                                <img src="{{asset('storage/no_image.png')}}" alt="Imagen" id="vista-imagen" style="width: 200px; height:200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Precios</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="precio_contado">Precio Contado <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('precio_contado') is-invalid @enderror" id="precio_contado" name="precio_contado" value="{{old('precio_contado')}}" placeholder="3.500.000">
                                            @error('precio_contado')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="cantidad_cuotas">Cantidad de Cuotas <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center @error('cantidad_cuotas') is-invalid @enderror" id="cantidad_cuotas" name="cantidad_cuotas" value="{{old('cantidad_cuotas')}}" placeholder="5">
                                            <span class="input-group-text">cuotas</span>
                                            @error('cantidad_cuotas')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="precio_cuota">Precio de Cuotas <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('precio_cuota') is-invalid @enderror" id="precio_cuota" name="precio_cuota" value="{{old('precio_cuota')}}" placeholder="500.000">
                                            @error('precio_cuota')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="fecha_inicio_vencimiento_cuota">Fecha 1er Vencimiento <span class="text-danger">(*)</span></label>
                                        <div class="form-icon right">
                                            <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_inicio_vencimiento_cuota') is-invalid @enderror" id="fecha_inicio_vencimiento_cuota" name="fecha_inicio_vencimiento_cuota" value="{{old('fecha_inicio_vencimiento_cuota')}}" placeholder="Seleccionar...">
                                            <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                            @error('fecha_inicio_vencimiento_cuota')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="dia_vencimiento_cuota">Día de Vencimiento <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Día</span>
                                            <input type="text" class="form-control text-center @error('dia_vencimiento_cuota') is-invalid @enderror" id="dia_vencimiento_cuota" name="dia_vencimiento_cuota" value="{{old('dia_vencimiento_cuota')}}" placeholder="10">
                                            <span class="input-group-text">de cada mes</span>
                                            @error('dia_vencimiento_cuota')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="precio_defensa">Precio de Defensa de Tesis <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('precio_defensa') is-invalid @enderror" id="precio_defensa" name="precio_defensa" value="{{old('precio_defensa')}}" placeholder="1.500.000">
                                            @error('precio_defensa')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="precio_titulo">Precio de Títulación <span class="text-danger">(*)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('precio_titulo') is-invalid @enderror" id="precio_titulo" name="precio_titulo" value="{{old('precio_titulo')}}" placeholder="1.500.000">
                                            @error('precio_titulo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.maestrias.scripts.create-scripts')
    @endsection
@endcan
