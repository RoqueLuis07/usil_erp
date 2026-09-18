@can('editar_horas_alumnos_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Editar Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensión Universitarias @endslot
            @slot('title') Editar Extensión Universitaria  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('extensiones_universitarias.update_hours', $extension->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar extensión universitaria</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="nombre_proyecto">Nombre del Proyecto</label>
                                    <input type="text" class="form-control" id="nombre_proyecto" value="{{$extension->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Responsable</label>
                                    <input type="text" class="form-control" id="docente" value="{{$extension->docente->primer_nombre}} {{$extension->docente->primer_apellido}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="tipo_extension">Tipo de Actividad</label>
                                    <input type="text" class="form-control" id="tipo_extension" value="{{$extension->tipoExtension->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cantidad_horas_proyecto">Horas del Proyecto</label>
                                    @php
                                        if ($extension->cantidad_horas > 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    <input type="text" class="form-control text-center" id="cantidad_horas_proyecto" value="{{number_format($extension->cantidad_horas, 2, ',', '.')}} {{$texto}}" readonly>
                                </div>
                                <input type="hidden" id="largo_detalles" value="{{$extension->extensionUniversitariaDetalles->count()}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Alumnos Participantes</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($extension->extensionUniversitariaDetalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-3 col-lg-2 mb-2 text-center" id="div-numero_documento-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-numero_documento">N° Documento</label> @endif
                                                    <input type="text" class="form-control text-center" id="numero_documento-{{$key}}" value="{{$detalle->alumno->numero_documento}}" readonly>
                                                </div>
                                                <div class="col-5 col-lg-3 mb-2 text-center" id="div-alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control text-center" id="alumno-{{$key}}" value="{{$detalle->alumno->primer_nombre}} {{$detalle->alumno->primer_apellido}}" readonly>
                                                    <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$detalle->alumno_id}}">
                                                </div>
                                                <div class="col-3 col-lg-2 text-center" id="div-cantidad_horas_alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-cantidad_horas_alumno">Cant. Horas <span class="text-danger">(*)</span></label> @endif
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-center @error('detalles.' . $key . '.cantidad_horas_alumno') is-invalid @enderror" id="cantidad_horas_alumno-{{$key}}" name="detalles[{{$key}}][cantidad_horas_alumno]" @if ($detalle->cantidad_horas) value="{{old('detalles.' . $key . '.cantidad_horas_alumno', number_format($detalle->cantidad_horas, 2, ',', '.'))}}" @else value="{{old('detalles.' . $key . '.cantidad_horas_alumno')}}" @endif>
                                                        <span class="input-group-text">horas</span>
                                                        @error('detalles.' . $key . '.cantidad_horas_alumno')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('extensiones_universitarias.show', $extension->id)}}">Cancelar</button>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('extensiones_universitarias.scripts.edit-hours-scripts')
    @endsection
@endcan
