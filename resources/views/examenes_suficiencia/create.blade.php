@can('crear_examenes_suficiencia')
    @extends('layouts.master')
    @section('title') Agregar Examen de Suficiencia @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Exámenes de Suficiencia @endslot
            @slot('title') Agregar Examen de Suficiencia  @endslot
        @endcomponent

        @include('examenes_suficiencia.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('examenes_suficiencia.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-8">
                                <h4 class="card-title mb-0">Nuevo examen de suficiencia</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="materia">Materia <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('materia') is-invalid @enderror" id="materia" name="materia" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($materias as $materia)
                                            <option value="{{$materia->materia_id}}" @if (old('materia') == strval($materia->materia_id)) selected @endif>{{$materia->materia->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('materia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="docente">Docente <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="docente_documento">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="docente_documento" name="docente_documento" value="{{old('docente_documento')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="modalidad">Modalidad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($modalidades as $modalidad)
                                            <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id)) selected @endif data-subtext="{{$modalidad->numero_documento}}">{{$modalidad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('modalidad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="fecha_examen">Fecha de Examen <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_examen') is-invalid @enderror" id="fecha_examen" name="fecha_examen" value="{{old('fecha_examen')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_examen"></i>
                                        @error('fecha_examen')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="aula_examen">Aula <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center @error('aula_examen') is-invalid @enderror" id="aula_examen" name="aula_examen" value="{{old('aula_examen')}}">
                                    @error('aula_examen')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="alumno">Alumno <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('alumno') is-invalid @enderror" id="alumno" name="alumno" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($alumnos as $alumno)
                                            <option value="{{$alumno->id}}" @if (old('alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="alumno_documento">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="alumno_documento" name="alumno_documento" value="{{old('alumno_documento')}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
                            <a type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</a>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js')}}"></script>
        @include('examenes_suficiencia.scripts.create-scripts')
    @endsection
@endcan
