@can('crear_solicitudes_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Realizar Solicitud @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_alumnos.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Realizar Solicitud</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 mb-3">
                                <label class="form-label" for="tipo_solicitud">Tipo de Solicitud <span class="text-danger">(*)</span></label>
                                <select class="form-control selectpicker @error('tipo_solicitud') is-invalid @enderror" id="tipo_solicitud" data-live-search="true">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    @foreach ($tipos_solicitudes as $tipo)
                                        @if ($tipo->id != 2)
                                            <option value="{{$tipo->id}}" @if (old('tipo_solicitud') == strval($tipo->id)) selected @endif>{{$tipo->nombre}}</option>
                                        @else
                                            @if (Carbon\Carbon::now()->between(Carbon\Carbon::parse($examen_suficiencia_fecha_solicitud->fecha_inicio), Carbon\Carbon::parse($examen_suficiencia_fecha_solicitud->fecha_fin)))
                                                <option value="{{$tipo->id}}" @if (old('tipo_solicitud') == strval($tipo->id)) selected @endif>{{$tipo->nombre}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                @error('tipo_solicitud')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
                <!-- end card -->
                @if ($tipos_solicitudes->where('id', 1)->count() > 0)
                    <div class="d-none" id="card-certificado-estudios">
                        <form action="{{route('solicitudes.store')}}" method="post" id="store-certificado-estudios-form">
                            @csrf
                            <input type="hidden" name="tipo_generacion" value="AL">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <input type="hidden" class="tipo_solicitud_form" name="tipo_solicitud" value="{{old('tipo_solicitud')}}">
                                        <div class="col-lg-12 mb-3" id="div-mensaje-certificado-estudios">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="observaciones">Observaciones</label>
                                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                            @error('observaciones')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-end mb-3">
                                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Cancelar</a>
                                            <button type="button" class="btn btn-success me-2 save-btn" data-id="certificado-estudios">Solicitar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                @if ($tipos_solicitudes->where('id', 2)->count() > 0)
                    <div class="d-none" id="card-suficiencia">
                        <form action="{{route('solicitudes.store')}}" method="post" id="store-suficiencia-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tipo_generacion" value="AL">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-3" id="div-mensaje-suficiencia">
    
                                        </div>
                                        <input type="hidden" class="tipo_solicitud_form" name="tipo_solicitud" value="{{old('tipo_solicitud')}}">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="materia_suficiencia">Materia <span class="text-danger">(*)</span></label>
                                            <span class="text-muted">Seleccione la materia que desea rendir el examen de suficiencia.</span>
                                            <div class="col-lg-6 mb-3">
                                                <select class="form-control selectpicker @error('materia_suficiencia') is-invalid @enderror" id="materia_suficiencia" name="materia_suficiencia" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($materias_suficiencias as $materia)
                                                        <option value="{{$materia->materia->id}}" @if (old('materia_suficiencia') == strval($materia->materia->id)) selected @endif>{{$materia->materia->nombre_fantasia}}</option>
                                                    @endforeach
                                                </select>
                                                @error('materia_suficiencia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="observaciones">Observaciones</label>
                                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                            @error('observaciones')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3" id="div-adjunto_suficiencia">
                                            <label class="form-label" for="adjunto_suficiencia">Adjunto</label>
                                            <span class="text-muted">Favor adjunte el archivo que justifique su solicitud.</span>
                                            <div class="input-group custom-file-button">
                                                <input type="file" class="form-control adjunto @error('adjunto_suficiencia') is-invalid @enderror" id="adjunto_suficiencia" name="adjunto_suficiencia" accept="application/pdf">
                                                <button type="button" class="btn btn-outline-danger eliminar-adjunto" id="eliminar-adjunto_suficiencia" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                                @error('adjunto_suficiencia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <p class="text-muted">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-end mb-3">
                                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Cancelar</a>
                                            <button type="button" class="btn btn-success me-2 save-btn" data-id="suficiencia">Solicitar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                @if ($tipos_solicitudes->where('id', 3)->count() > 0)
                    <div class="d-none" id="card-inasistencia">
                        <form action="{{route('solicitudes.store')}}" method="post" id="store-inasistencia-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tipo_generacion" value="AL">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="row d-flex flex-wrap justify-content-between">
                                            <div class="col-lg-7 mb-3" id="div-mensaje-inasistencia">

                                            </div>
                                            <div class="col-lg-5">
                                                <p class="text-danger">Recuerde que el máximo de solicitudes por semestres es de 3 y usted ya cuenta con {{$cantidad_inasistencias}} @if ($cantidad_inasistencias != 1) realizadas @else realizada @endif.</p>
                                            </div>
                                        </div>
                                        <input type="hidden" class="tipo_solicitud_form" name="tipo_solicitud" value="{{old('tipo_solicitud')}}">
                                        <div class="col-lg-5 mb-3">
                                            <label class="form-label" for="materia_inasistencia">Materia <span class="text-danger">(*)</span></label>
                                            <span class="text-muted">Seleccione la materia en la que estará ausente.</span>
                                            <div class="col-lg-6 mb-3">
                                                <select class="form-control selectpicker @error('materia_inasistencia') is-invalid @enderror" id="materia_inasistencia" name="materia_inasistencia" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($materias as $materia)
                                                        <option value="{{$materia->id}}" @if (old('materia_inasistencia') == strval($materia->id)) selected @endif>{{$materia->nombre_fantasia}}</option>
                                                    @endforeach
                                                </select>
                                                @error('materia_inasistencia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3">
                                            <label class="form-label" for="fecha_inasistencia">Fecha <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control form-control-icon flatpickr text-center @error('fecha_inasistencia') is-invalid @enderror" id="fecha_inasistencia" name="fecha_inasistencia" value="{{old('fecha_inasistencia')}}">
                                            @error('fecha_inasistencia')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-5 mb-3">
                                            <label class="form-label" for="observaciones">Observaciones</label>
                                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                            @error('observaciones')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3" id="div-adjunto_inasistencia">
                                            <label class="form-label" for="adjunto_inasistencia">Adjunto <span class="text-danger">(*)</span></label>
                                            <span class="text-muted">Favor adjunte el archivo que justifique su solicitud.</span>
                                            <div class="input-group custom-file-button">
                                                <input type="file" class="form-control adjunto @error('adjunto_inasistencia') is-invalid @enderror" id="adjunto_inasistencia" name="adjunto_inasistencia" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);">
                                                <button type="button" class="btn btn-outline-danger eliminar-adjunto" id="eliminar-adjunto_inasistencia" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                                @error('adjunto_inasistencia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <p class="text-muted">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code>, <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-end mb-3">
                                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Cancelar</a>
                                            <button type="button" class="btn btn-success me-2 save-btn" data-id="inasistencia">Solicitar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                @if ($tipos_solicitudes->where('id', 4)->count() > 0)
                    <div class="d-none" id="card-constancia-carrera">
                        <form action="{{route('solicitudes.store')}}" method="post" id="store-constancia-carrera-form">
                            @csrf
                            <input type="hidden" name="tipo_generacion" value="AL">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <input type="hidden" class="tipo_solicitud_form" name="tipo_solicitud" value="{{old('tipo_solicitud')}}">
                                        <div class="col-lg-12 mb-3" id="div-mensaje-constancia-carrera">

                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="observaciones">Observaciones</label>
                                        <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                        @error('observaciones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-end mb-3">
                                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Cancelar</a>
                                            <button type="button" class="btn btn-success me-2 save-btn" data-id="constancia-carrera">Solicitar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                @if ($tipos_solicitudes->where('id', 5)->count() > 0)
                    <div class="d-none" id="card-tutoria">
                        <form action="{{route('solicitudes.store')}}" method="post" id="store-tutoria-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tipo_generacion" value="AL">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-3" id="div-mensaje-tutoria">

                                        </div>
                                        <input type="hidden" class="tipo_solicitud_form" name="tipo_solicitud" value="{{old('tipo_solicitud')}}">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="modalidad_tutoria">Modalidad <span class="text-danger">(*)</span></label>
                                            <span class="text-muted">Seleccione la modalidad que desea tomar la tutoria.</span>
                                            <div class="col-lg-6 mb-3">
                                                <select class="form-control selectpicker @error('modalidad_tutoria') is-invalid @enderror" id="modalidad_tutoria" name="modalidad_tutoria" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($modalidades_tutorias as $modalidad)
                                                        <option value="{{$modalidad->modalidad->id}}" @if (old('modalidad_tutoria') == strval($modalidad->modalidad->id)) selected @endif>{{$modalidad->modalidad->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('modalidad_tutoria')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="materia_tutoria">Materia <span class="text-danger">(*)</span></label>
                                            <span class="text-muted">Seleccione la materia que desea tomar la tutoria.</span>
                                            <div class="col-lg-6 mb-3">
                                                <select class="form-control selectpicker @error('materia_tutoria') is-invalid @enderror" id="materia_tutoria" name="materia_tutoria" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($materias_tutorias as $materia)
                                                        <option value="{{$materia->id}}" @if (old('materia_tutoria') == strval($materia->id)) selected @endif>{{$materia->nombre_fantasia}}</option>
                                                    @endforeach
                                                </select>
                                                @error('materia_tutoria')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="observaciones">Observaciones</label>
                                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                            @error('observaciones')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-end mb-3">
                                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Cancelar</a>
                                            <button type="button" class="btn btn-success me-2 save-btn" data-id="tutoria">Solicitar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                @if ($tipos_solicitudes->where('id', 6)->count() > 0)
                    <div class="d-none" id="card-desmatriculacion">
                        <form action="{{route('solicitudes.store')}}" method="post" id="store-desmatriculacion-form">
                            @csrf
                            <input type="hidden" name="tipo_generacion" value="AL">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <input type="hidden" class="tipo_solicitud_form" name="tipo_solicitud" value="{{old('tipo_solicitud')}}">
                                        <div class="col-lg-12 mb-3" id="div-mensaje-desmatriculacion">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label class="form-label" for="materia_desmatriculacion">Materia <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('materia_desmatriculacion') is-invalid @enderror" id="materia_desmatriculacion" name="materia_desmatriculacion" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($inscripciones as $inscripcion)
                                                    <option value="{{ $inscripcion->materia_id }}" @if (old('materia_desmatriculacion') == strval($inscripcion->materia_id)) selected @endif>{{$inscripcion->materia->nombre_fantasia}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="observaciones">Observaciones</label>
                                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                            @error('observaciones')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-end mb-3">
                                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Cancelar</a>
                                            <button type="button" class="btn btn-success me-2 save-btn" data-id="constancia-carrera">Solicitar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/dayjs.min.js') }}"></script>
        @include('pantallas_alumnos.scripts.create_solicitudes-scripts')
    @endsection
@endcan
