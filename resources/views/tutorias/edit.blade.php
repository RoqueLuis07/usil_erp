@can('editar_tutorias')
    @extends('layouts.master')
    @section('title') Editar Tutoría @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Tutorías @endslot
            @slot('title') Editar Tutoría  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('tutorias.update', $tutoria->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Actualizar tutoría</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$tutoria->materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id) || $tutoria->docente_id == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="modalidad">Modalidad</label>
                                    <input type="text" class="form-control" id="modalidad" value="{{$tutoria->modalidad->nombre}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 mb-3 me-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$tutoria->semestre->nombre}}" readonly>
                                    </div>
                                    <div class="col-lg-3 mb-3 text-center">
                                        <label class="form-label" for="cantidad_horas">Cant. Horas</label>
                                        <input type="text" class="form-control text-center" id="cantidad_horas" value="{{$tutoria->cantidad_horas}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha Inicio <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio', $tutoria->fecha_inicio)}}">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_inicio"></i>
                                        @error('fecha_inicio')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha Fin <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin', $tutoria->fecha_fin)}}">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_fin"></i>
                                        @error('fecha_fin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cantidad_clases">Cant. Clases <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center @error('cantidad_clases') is-invalid @enderror" id="cantidad_clases" name="cantidad_clases" value="{{old('cantidad_clases', $tutoria->cantidad_clases)}}">
                                    @error('cantidad_clases')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('tutorias.scripts.edit-scripts')
    @endsection
@endcan
