@can('cargar_rubricas_alumnos_defensas_tesis')
    @extends('layouts.master')
    @section('title') Agregar Rúbrica de TFG @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Tema de Trabajo Final de Grado @endslot
            @slot('title') Agregar Rúbrica de TFG  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('cargar_rubricas_tesis.store_defensa', $inscripcion->id)}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva rúbrica de trabajo final de grado</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <input type="hidden" id="inscripcion_id" value="{{$inscripcion->id}}">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ci_alumno">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="ci_alumno" value="{{number_format($inscripcion->alumno->numero_documento, 0, ',', '.')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3"></div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control" id="tutor" value="{{$inscripcion->tutor->primer_nombre}} {{$inscripcion->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ci_tutor">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="ci_tutor" value="{{number_format($inscripcion->tutor->numero_documento, 0, ',', '.')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5 mb-5">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$inscripcion->tema}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-5"></div>
                                <div class="col-lg-3 mb-5">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control" id="tipo" value="{{$inscripcion->tipo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-5">
                                    <label class="form-label" for="fecha_defensa">Fecha Defensa</label>
                                    <input type="text" class="form-control text-center" id="fecha_defensa" value="{{\Carbon\Carbon::parse($inscripcion->fecha_defensa)->format('d/m/Y H:i')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-1">
                                        <label class="form-label" for="total_posible">Total Posible</label>
                                        <input class="form-control text-center" type="text" id="total_posible" value="{{$total_posible}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Puntajes de la Rúbrica</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-2 mb-3">
                                            <label class="form-label" for="puntaje_obtenido">Puntos Obtenidos <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control text-center puntaje_obtenido @error('puntaje_obtenido') is-invalid @enderror" id="puntaje_obtenido" name="puntaje_obtenido" value="{{old('puntaje_obtenido')}}" placeholder="0 - {{$total_posible}}">
                                            @error('puntaje_obtenido')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label" for="div-acta">Acta Completo <span class="text-danger">(*)</span></label>
                                            <div class="input-group custom-file-button" id="div-acta">
                                                <input type="file" class="form-control @error('acta') is-invalid @enderror" id="acta" name="acta" accept="application/pdf">
                                                <button type="button" class="btn btn-outline-danger" id="eliminar-acta" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                                @error('acta')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <p class="text-muted">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('tesis.cargar_rubricas.defensa.scripts.create-scripts')
    @endsection
@endcan
