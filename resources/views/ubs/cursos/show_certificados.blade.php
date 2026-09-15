@can('generar_certificados_cursos_ubs')
    @extends('layouts.master')
    @section('title') Generar Certificados del Curso @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Cursos @endslot
            @slot('title') Generar Certificados del Curso  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('cursos_certificados.generate', $curso->id)}}" method="get" target="_blank" id="generate-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Generar certificados del curso</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="nombre_fantasia">Nombre Fantasía</label>
                                                <input type="text" class="form-control" id="nombre_fantasia" value="{{$curso->nombre_fantasia}}" readonly>
                                            </div>
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="nombre_real">Nombre Real</label>
                                                <input type="text" class="form-control" id="nombre_real" value="{{$curso->nombre_real}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="codigo">Código</label>
                                                <input type="text" class="form-control" id="codigo" value="{{$curso->codigo}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="modalidad">Modalidad</label>
                                                <input type="text" class="form-control" id="modalidad" value="{{$curso->modalidad->nombre}}" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="fecha_apertura">Fecha de Apertura</label>
                                                <input type="text" class="form-control text-center" id="fecha_apertura" value="{{Carbon\Carbon::parse($curso->fecha_apertura)->format('d/m/Y')}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="fecha_fin">Fecha de Fin</label>
                                                <input type="text" class="form-control text-center" id="fecha_fin" value="{{Carbon\Carbon::parse($curso->fecha_fin)->format('d/m/Y')}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="cantidad_horas">Cantidad de Horas</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-center" id="cantidad_horas" value="{{number_format($curso->cantidad_horas, 0, ',', '.')}}" readonly>
                                                    <span class="input-group-text">horas</span>
                                                </div>
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
                                <h4 class="card-title mb-0">Lista de Alumnos</h4>
                            </div>
                            <div class="card-body">
                                @forelse ($alumnos as $key => $alumno)
                                <div class="mb-2 fila" id="fila-{{$key}}">
                                    <div class="row d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-3 mb-2 text-center" id="div-alumno-{{$key}}">
                                            @if ($key == 0) <label class="form-label label-alumno">Alumno</label> @endif
                                            <input type="text" class="form-control text-center" id="alumno-{{$key}}" value="{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}" readonly>
                                            <input type="hidden" name="detalles[{{$key}}][alumno]" value="{{$alumno->id}}" readonly>
                                            <input type="hidden" name="detalles[{{$key}}][numero_inscripcion]" value="{{$alumno->numero_inscripcion}}" readonly>
                                        </div>
                                        <div class="col-lg-2 mb-2 text-center" id="div-numero_documento-{{$key}}">
                                            @if ($key == 0) <label class="form-label label-numero_documento">N° de Documento</label> @endif
                                            <input type="text" class="form-control text-center" id="numero_documento-{{$key}}" value="{{number_format($alumno->numero_documento, 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-2 mb-2 me-3 text-center" id="div-numero_orden-{{$key}}">
                                            @if ($key == 0) <label class="form-label label-numero_orden">N° de Orden <span class="text-danger">(*)</span></label> @endif
                                            <input type="text" class="form-control text-center numero_orden-{{$key}} numero_orden @error('detalles.'. $key . '.numero_orden') is-invalid @enderror" id="detalles[{{$key}}][numero_orden]" name="detalles[{{$key}}][numero_orden]" value="{{old('detalles.' . $key . '.numero_orden')}}" data-id="{{$key}}">
                                            @error('detalles.'. $key . '.numero_orden')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-2 mb-2 me-3 text-center" id="div-pagina-{{$key}}">
                                            @if ($key == 0) <label class="form-label label-pagina">N° de Página <span class="text-danger">(*)</span></label> @endif
                                            <input type="text" class="form-control text-center pagina-{{$key}} pagina @error('detalles.'. $key . '.pagina') is-invalid @enderror" id="detalles[{{$key}}][pagina]" name="detalles[{{$key}}][pagina]" value="{{old('detalles.' . $key . '.pagina')}}" data-id="{{$key}}">
                                            @error('detalles.'. $key . '.pagina')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @empty
                                    <p class="text-center">No existen alumnos inscriptos en el curso que hayan cumplido con los requisitos para generar sus certificados.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success me-2" id="generate-btn" @if ($alumnos->count() == 0) disabled @endif>Generar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.cursos.scripts.show_certificados-scripts')
    @endsection
@endcan
