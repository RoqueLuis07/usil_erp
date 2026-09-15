@can('crear_anteproyectos_tesis_ubs')
    @extends('layouts.master')
    @section('title') Generar Anteproyecto de Tesis @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ver Tesis @endslot
            @slot('title') Generar Anteproyecto de Tesis @endslot
        @endcomponent

        @include('ubs.tesis.anteproyectos.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('tesis_ubs.store_anteproyecto', $tesis->id)}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Generar anteproyecto de tesis</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$tesis->alumno->primer_nombre}} {{$tesis->alumno->primer_apellido}} " readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="alumno_documento">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="alumno_documento" value="{{$tesis->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="maestria">Maestría</label>
                                    <input type="text" class="form-control" id="maestria" value="{{$tesis->curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="llamado">Llamado</label>
                                    <input type="text" class="form-control" id="llamado" value="{{$tesis->curso->llamado}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="estado">Estado</label>
                                        <input type="text" class="form-control text-center fw-bold
                                            @if ($tesis->estado == 'AC' || $tesis->estado == 'AT' || $tesis->estado == 'AC' || $tesis->estado == 'AA' || $tesis->estado == 'BP' || $tesis->estado == 'PA' || $tesis->estado == 'EN')
                                                text-success
                                            @elseif ($tesis->estado == 'EC' || $tesis->estado == 'BC' || $tesis->estado == 'FE')
                                                text-warning
                                            @elseif ($tesis->estado == 'RR')
                                                text-danger
                                            @endif"
                                            id="estado"
                                            @if ($tesis->estado == 'AC')
                                                value="APROBADO POR CALIDAD"
                                            @elseif ($tesis->estado == 'EC')
                                                value="EN CURSO"
                                            @elseif ($tesis->estado == 'BC')
                                                value="BORRADOR EN CURSO"
                                            @elseif ($tesis->estado == 'AA')
                                                value="APROBADO"
                                            @elseif ($tesis->estado == 'BP')
                                                value="BORRADOR APROBADO"
                                            @elseif ($tesis->estado == 'PA')
                                                value="PAGADO"
                                            @elseif ($tesis->estado == 'FE')
                                                value="DEFENSA"
                                            @elseif ($tesis->estado == 'EN')
                                                value="APROBADO"
                                            @elseif ($tesis->estado == 'RR')
                                                value="REPROBADO"
                                            @endif
                                        readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="linea">Línea</label>
                                    <input type="text" class="form-control" id="linea" value="{{$tesis->linea->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control" id="tutor" value="{{$tesis->tutor->primer_nombre}} {{$tesis->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="tutor_documento">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="tutor_documento" value="{{$tesis->tutor->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$tesis->tema}}" readonly>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="titulo">Título</label>
                                    <textarea class="form-control" id="titulo" cols="30" rows="3" readonly>{{$tesis->titulo}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Asignar Módulos a los Bloques</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito.</p>
                                    @foreach ($bloques as $key => $bloque)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-1 mb-2 text-center" id="div-numero-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-numero">N°</label> @endif
                                                    <input type="text" class="form-control text-center" id="numero-{{$key}}" value="{{$bloque->numero}}" readonly>
                                                </div>
                                                <div class="col-lg-3 mb-2 text-center" id="div-nombre-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-nombre">Bloque</label> @endif
                                                    <input type="text" class="form-control text-center" id="nombre-{{$key}}" value="{{$bloque->nombre}}" readonly>
                                                    <input type="hidden" name="detalles[{{$key}}][bloque]" value="{{$bloque->id}}">
                                                </div>
                                                <div class="col-lg-3 mb-2 text-center" id="div-modulo-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-modulo">Módulo <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control @error('detalles.' . $key . '.modulo') is-invalid @enderror" id="modulo-{{$key}}" name="detalles[{{$key}}][modulo]" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($modulos as $modulo)
                                                            <option value="{{$modulo->modulo->id}}" @if (old('detalles.' . $key . '.modulo') == strval($modulo->id)) selected @endif data-subtext="{{$modulo->modulo->nombre_real}}">{{$modulo->modulo->nombre_fantasia}}</option>
                                                        @endforeach
                                                    </select>
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
                            <a type="button" class="btn btn-danger me-2" href="{{route('tesis_ubs.show', $tesis->id)}}">Volver</a>
                            <button type="button" class="btn btn-success" id="save-btn">Generar</button>
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
        @include('ubs.tesis.anteproyectos.scripts.create-scripts')
    @endsection
@endcan
