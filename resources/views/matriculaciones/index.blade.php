@can('ver_matriculaciones')
    @extends('layouts.master')
    @section('title') Matriculaciones @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Matriculaciones @endslot
        @endcomponent

        @include('matriculaciones.scripts.messages-scripts')
        @include('matriculaciones.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-0">Lista de Matriculaciones</h4>
                        </div>
                        @can('ver_horarios_semestres_matriculaciones')
                            <div class="col-lg-6 text-end">
                                <a type="button" class="btn btn-warning" href="{{route('matriculaciones.show_horarios')}}">Ver Horarios</a>
                            </div>
                        @endcan
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-lg-1">
                                    @can('crear_matriculaciones')
                                        <a type="button" class="btn btn-success" href="{{route('matriculaciones.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_periodo" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Período...</option>
                                            @foreach ($semestres as $semestre)
                                                <option value="{{$semestre->id}}" @if ($filtro_periodo == $semestre->id) selected @endif>{{$semestre->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-periodo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="d-flex justify-content-sm-end">
                                        <form autocomplete="off" method="POST" action="{{ route('matriculaciones.index') }}" id="buscar_form">
                                            @csrf
                                            <div class="search-box ms-2">
                                                <input type="text" id="buscar" name="buscar" value="{{$buscar}}" class="form-control search" placeholder="Buscar...">
                                                <input type="hidden" id="filtro_periodo_input" name="filtro_periodo" value="{{$filtro_periodo}}">
                                                <input type="hidden" id="filtro_fecha_input" name="filtro_fecha" value="{{$filtro_fecha}}">
                                                <input type="hidden" id="filtro_alumno_input" name="filtro_alumno" value="{{$filtro_alumno}}">
                                                <input type="hidden" id="filtro_ingreso_input" name="filtro_ingreso" value="{{$filtro_ingreso}}">
                                                <input type="hidden" id="filtro_programa_input" name="filtro_programa" value="{{$filtro_programa}}">
                                                <input type="hidden" id="filtro_carrera_input" name="filtro_carrera" value="{{$filtro_carrera}}">
                                                <input type="hidden" id="filtro_carrera_siu_input" name="filtro_carrera_siu" value="{{$filtro_carrera_siu}}">
                                                <i class="ri-search-line search-icon"></i>
                                                <button class="btn btn-primary py-0 d-none" type="submit">
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_fecha" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Fecha...</option>
                                            <option value="01-02" @if ($filtro_fecha == '01-02') selected @endif>ENE - FEB</option>
                                            <option value="02-03" @if ($filtro_fecha == '02-03') selected @endif>FEB - MAR</option>
                                            <option value="03-04" @if ($filtro_fecha == '03-04') selected @endif>MAR - ABR</option>
                                            <option value="04-05" @if ($filtro_fecha == '04-05') selected @endif>ABR - MAY</option>
                                            <option value="05-06" @if ($filtro_fecha == '05-06') selected @endif>MAY - JUN</option>
                                            <option value="06-07" @if ($filtro_fecha == '06-07') selected @endif>JUN - JUL</option>
                                            <option value="07-08" @if ($filtro_fecha == '07-08') selected @endif>JUL - AGO</option>
                                            <option value="08-09" @if ($filtro_fecha == '08-09') selected @endif>AGO - SEPT</option>
                                            <option value="09-10" @if ($filtro_fecha == '09-10') selected @endif>SEPT - OCT</option>
                                            <option value="10-11" @if ($filtro_fecha == '10-11') selected @endif>OCT - NOV</option>
                                            <option value="11-12" @if ($filtro_fecha == '11-12') selected @endif>NOV - DIC</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-fecha-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Fecha"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_alumno" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Alumno...</option>
                                            @foreach ($alumnos as $alumno)
                                                <option value="{{$alumno->id}}" @if ($filtro_alumno == $alumno->id) selected @endif data-subtext={{ $alumno->numero_documento }}>{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-alumno-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Alumno"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_ingreso" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Ingreso...</option>
                                            @foreach ($semestres as $semestre)
                                                <option value="{{$semestre->id}}" @if ($filtro_ingreso == $semestre->id) selected @endif>{{$semestre->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-ingreso-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Ingreso"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_programa" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Programa...</option>
                                            @foreach ($programas as $programa)
                                                <option value="{{$programa->id}}" @if ($filtro_programa == $programa->id) selected @endif>{{$programa->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-programa-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Programa"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_carrera" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Carrera...</option>
                                            @foreach ($carreras as $carrera)
                                                <option value="{{$carrera->id}}" @if ($filtro_carrera == $carrera->id) selected @endif data-subtext="{{ $carrera->programa->nombre }}">{{$carrera->nombre_fantasia}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-carrera-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Carrera"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_carrera_siu" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Carrera SIU...</option>
                                            @foreach ($carreras_siu as $carrera_siu)
                                                <option value="{{$carrera_siu->id}}" @if ($filtro_carrera_siu == $carrera_siu->id) selected @endif>{{$carrera_siu->nombre_fantasia}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-carrera_siu-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Carrera SIU"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Alumno</th>
                                            <th>Semestre</th>
                                            <th>Programa</th>
                                            <th>Carrera</th>
                                            <th>Carrera SIU</th>
                                            <th>Ingreso</th>
                                            <th class="text-center">Materias</th>
                                            <th class="text-center">Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="form-check-all">
                                        @forelse ($matriculaciones as $matriculacion)
                                            <tr>
                                                <td>{{$matriculacion->id}}</td>
                                                <td>{{\Carbon\Carbon::parse($matriculacion->fecha)->format('d/m/Y H:i:s')}}</td>
                                                <td>{{$matriculacion->alumno->primer_nombre}} {{$matriculacion->alumno->primer_apellido}} - {{$matriculacion->alumno->numero_documento}}</td>
                                                <td>{{$matriculacion->semestre->nombre}}</td>
                                                <td>{{$matriculacion->programa->nombre}}</td>
                                                <td>{{$matriculacion->carrera->nombre_fantasia}}</td>
                                                <td>
                                                    @if ($matriculacion->carrera_siu_id)
                                                        {{$matriculacion->carreraSiu->nombre_fantasia}}
                                                    @endif
                                                </td>
                                                <td>{{$matriculacion->ingreso}}</td>
                                                <td class="text-center">{{$matriculacion->inscripciones->count()}}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge @if ($matriculacion->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($matriculacion->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('matriculaciones.show', $matriculacion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('ver_inscripciones_matriculaciones')
                                                        <a type="button" class="btn btn-sm @if ($matriculacion->inscripciones()->count() > 0) btn-success @elseif ($matriculacion->inscripciones()->count() == 0) btn-warning @endif" href="{{route('inscripciones.show', $matriculacion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Inscripción"><i class="ri-list-check"></i></a>
                                                    @endcan
                                                    @if ($matriculacion->estado == 'AC')
                                                        @can('inactivar_matriculaciones')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$matriculacion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($matriculacion->estado == 'IN')
                                                        @can('activar_matriculaciones')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$matriculacion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_matriculaciones')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$matriculacion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningúna matriculación según tus parámetros de búsqueda.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                @if ($matriculaciones->hasPages())
                                    <nav class="justify-content-lg-end">
                                        {{ $matriculaciones->links('pagination::bootstrap-5') }}
                                    </nav>
                                @else
                                    <p class="small text-muted">
                                        Mostrando <span class="fw-semibold">{{$matriculaciones->count()}}</span>
                                        a <span class="fw-semibold">{{$matriculaciones->count()}}</span>
                                        de <span class="fw-semibold">{{$matriculaciones->count()}}</span>
                                        resultados
                                    </p>
                                @endif
							</div>
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        {{-- <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script> --}}
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('matriculaciones.scripts.index-scripts')
    @endsection
@endcan
