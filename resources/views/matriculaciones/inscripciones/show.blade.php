@can('ver_inscripciones_matriculaciones')
    @extends('layouts.master')
    @section('title') Ver Inscripción @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Matriculaciones @endslot
            @slot('title') Ver Inscripción  @endslot
        @endcomponent

        @include('matriculaciones.scripts.messages-scripts')
        @include('matriculaciones.inscripciones.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar inscripción</h4>
                            </div>
                            @if ($matriculacion->estado == 'AC')
                                <div class="col-lg-6 text-end" style="margin-bottom: -5em">
                                    <div class="d-flex justify-content-end">
                                        <a type="button" class="btn @if ($matriculacion->inscripciones()->count() != 0) btn-warning @else btn-success @endif me-2" href="{{route('inscripciones.edit', $matriculacion->id)}}">@if ($matriculacion->inscripciones()->count() != 0) Editar @else Agregar @endif Materias</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control" id="fecha" value="{{\Carbon\Carbon::parse($matriculacion->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$matriculacion->alumno->primer_nombre}} {{$matriculacion->alumno->primer_apellido}} - {{$matriculacion->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="semestre">Semestre</label>
                                    <input type="text" class="form-control" id="semestre" value="{{$matriculacion->semestre->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$matriculacion->programa->nombre}}" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="carrera">Carrera</label>
                                        <input type="text" class="form-control" id="carrera" value="{{$matriculacion->carrera->nombre_fantasia}}" readonly>
                                    </div>
                                    @if ($matriculacion->programa_id == 1)
										<div class="col-lg-3 mb-3">
											<label class="form-label" for="carrera">Carrera SIU</label>
											<input type="text" class="form-control" id="carrera" @if ($matriculacion->carrera_siu_id) value="{{$matriculacion->carreraSiu->nombre_fantasia}}" @endif readonly>
										</div>
									@endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Materias Inscriptas</h4>
                                    </div>
                                    @if ($matriculacion->inscripciones->count() > 0 && $matriculacion->estado == 'AC')
                                        <div class="col-lg-6 text-end">
                                            <div class="d-flex justify-content-end">
                                                <a type="button" class="btn btn-warning me-2" href="{{route('inscripciones.show_horarios', $matriculacion->id)}}">Ver Horario</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @forelse ($matriculacion->inscripciones as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-3 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="materia">Materia</label> @endif
                                                    <input type="text" class="form-control" id="materia" @if ($detalle->materia_id) value="{{$detalle->materia->nombre_fantasia}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-3 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="docente">Docente</label> @endif
                                                    <input type="text" class="form-control text-center" id="docente" @if ($detalle->docente_id) value="{{$detalle->docente->primer_nombre}} {{$detalle->docente->primer_apellido}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-3 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="estado">Estado</label> @endif
                                                    <input type="text" id="estado" class="form-control text-center fw-bold
                                                        @if ($detalle->estado == 'MA')
                                                            text-warning text-uppercase"
                                                            value="Matriculado"
                                                        @elseif ($detalle->estado == 'EC')
                                                            text-warning text-uppercase"
                                                            value="En Curso"
                                                        @elseif ($detalle->estado == 'AP')
                                                            text-success text-uppercase"
                                                            value="Aprobado"
                                                        @elseif ($detalle->estado == 'RE')
                                                            text-danger text-uppercase"
                                                            value="Reprobado"
                                                        @elseif ($detalle->estado == 'CO')
                                                            text-success text-uppercase"
                                                            value="Convalidado"
                                                        @elseif ($detalle->estado == 'ES')
                                                            text-success text-uppercase"
                                                            value="Espejo"
                                                        @elseif ($detalle->estado == 'SU')
                                                            text-success text-uppercase"
                                                            value="Suficiencia"
                                                        @elseif ($detalle->estado == 'DE')
                                                            text-danger text-uppercase"
                                                            value="Desmatriculado"
                                                        @endif
                                                    readonly>
                                                </div>
                                                @if (($detalle->estado == 'MA' || $detalle->estado == 'EC') && $matriculacion->estado == 'AC')
                                                    @can('desmatricular_inscripciones_matriculaciones')
                                                        <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                            @if ($key == 0) <label class="form-label" for="acciones">Acciones</label> @endif
                                                            <div class="d-flex justify-content-center">
                                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Desmatricular"><i class="ri-close-fill"></i></button>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                @elseif ($detalle->estado == 'DE' && $matriculacion->estado == 'AC')
                                                     @can('desmatricular_inscripciones_matriculaciones')
                                                        <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                            @if ($key == 0) <label class="form-label" for="acciones">Acciones</label> @endif
                                                            <div class="d-flex justify-content-center">
                                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Desmatriculación"><i class="ri-close-fill"></i></button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                        </div>
                                                    @endcan
                                                @else
                                                    <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                    <div class="mb-2">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-12 col-sm-12 mb-2 text-center">
                                                <p>El alumno no cuenta con ninguna materia en el presente semestre.</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$matriculacion->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($matriculacion->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($matriculacion->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$matriculacion->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($matriculacion->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('matriculaciones.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('matriculaciones.inscripciones.scripts.show-scripts')
    @endsection
@endcan
