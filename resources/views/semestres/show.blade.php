@can('ver_periodos')
    @extends('layouts.master')
    @section('title') Ver Semestre @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Semestres @endslot
            @slot('title') Ver Semestre  @endslot
        @endcomponent

        @include('semestres.modals.show-modals')
        @include('semestres.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar semestre</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre_semestre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre_semestre" value="{{$semestre->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha de Inicio</label>
                                    <input type="text" class="form-control" id="fecha_inicio" value="{{\Carbon\Carbon::parse($semestre->fecha_inicio)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha de Fin</label>
                                    <input type="text" class="form-control" id="fecha_fin" value="{{\Carbon\Carbon::parse($semestre->fecha_fin)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_carreras">Cant. de Carreras</label>
                                        <input class="form-control text-center" type="text" id="cantidad_carreras" value="{{number_format($semestre->semestreMallas->count(), 0, ',', '.')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Carreras Habilitadas del Semestre</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($semestre->semestreMallas as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="malla">Carrera</label> @endif
                                                    <input type="text" class="form-control" id="malla" value="{{$detalle->malla->carrera->nombre_fantasia}} - {{$detalle->malla->carrera->programa->nombre}}">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="coordinador">Coordinador</label> @endif
                                                    <input type="text" class="form-control" id="coordinador" value="{{$detalle->coordinador->primer_nombre}} {{$detalle->coordinador->primer_apellido}}">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="fecha_inicio_matriculacion">Inicio Matriculación</label> @endif
                                                    <input type="text" class="form-control text-center" id="fecha_inicio_matriculacion" value="{{\Carbon\Carbon::parse($detalle->fecha_inicio_matriculacion)->format('d/m/Y')}}">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="fecha_fin_matriculacion">Fin Matriculación</label> @endif
                                                    <input type="text" class="form-control text-center" id="fecha_fin_matriculacion" value="{{\Carbon\Carbon::parse($detalle->fecha_fin_matriculacion)->format('d/m/Y')}}">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <div><label class="form-label" for="doble_grado">Acciones</label></div> @endif
                                                    @can('ver_materias_periodos')
                                                        <a type="button" class="btn btn-sm btn-primary" href="{{route('semestres_mallas_materias.show', $detalle->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Materias de la Carrera"><i class="ri-eye-fill"></i></a>
                                                    @endcan
                                                    @if ($detalle->semestre->estado != 'IN')
                                                        @can('editar_parametros_carreras_periodos')
                                                            <button type="button" class="btn btn-sm btn-info edit-semestre-malla" data-bs-toggle="modal" data-bs-target="#editSemestreMallaModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar Detalles de la Carrera" data-id="{{$detalle->id}}"><i class="ri-edit-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$semestre->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($semestre->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($semestre->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$semestre->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($semestre->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('semestres.index')}}">Volver</a>
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
        @include('semestres.scripts.show-scripts')
    @endsection
@endcan
