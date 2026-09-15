@can('ver_materias_clases')
    @extends('layouts.master')
    @section('title') Clases @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Clases @endslot
        @endcomponent

        @include('clases_materias.scripts.messages-scripts')
        @include('clases_materias.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Clases</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="row g-4 mb-3 d-flex justify-content-end">
                            <div class="col-lg-2">
                                <form autocomplete="off" method="POST" action="{{ route('clases_materias.index') }}" id="buscar_form">
                                @csrf
                                    <div class="search-box ms-2">
                                        <input type="text" id="buscar" name="buscar" value="{{$buscar}}" class="form-control search" placeholder="Buscar...">
                                        <input type="hidden" id="filtro_fecha_input" name="filtro_fecha" value="{{$filtro_fecha}}">
                                        <input type="hidden" id="filtro_materia_input" name="filtro_materia" value="{{$filtro_materia}}">
                                        <input type="hidden" id="filtro_carrera_input" name="filtro_carrera" value="{{$filtro_carrera}}">
                                        <input type="hidden" id="filtro_docente_input" name="filtro_docente" value="{{$filtro_docente}}">
                                        <input type="hidden" id="filtro_periodo_input" name="filtro_periodo" value="{{$filtro_periodo}}">
                                        <input type="hidden" id="filtro_inscriptos_input" name="filtro_inscriptos" value="{{$filtro_inscriptos}}">
                                        <i class="ri-search-line search-icon"></i>
                                        <button class="btn btn-primary py-0 d-none" type="submit">
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row g-4 mb-3">
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <button class="btn btn-sm btn-outline-danger" id="delete-fecha-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Fecha"><i class="ri-close-line"></i></button>
                                    <input type="text" class="flatpickr form-control form-control-icon text-center " id="filtro_fecha" value="{{$filtro_fecha}}" placeholder="Filtrar por Fecha">
                                    <button class="btn btn-sm btn-outline-info" id="search-fecha-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Buscar por Fecha"><i class="ri-search-line"></i></button>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <select class="selectpicker form-control" id="filtro_materia" data-live-search="true">
                                        <option value="" selected disabled>Filtrar por Materia...</option>
                                        @foreach ($materias as $materia)
                                            <option value="{{$materia->id}}" @if ($filtro_materia == $materia->id) selected @endif>{{$materia->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-danger" id="delete-materia-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Materia"><i class="ri-close-line"></i></button>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <select class="selectpicker form-control" id="filtro_carrera" data-live-search="true">
                                        <option value="" selected disabled>Filtrar por Carrera...</option>
                                        @foreach ($carreras as $carrera)
                                            <option value="{{$carrera->nombre_fantasia}}" @if ($filtro_carrera == $carrera->nombre_fantasia) selected @endif data-subtext="{{ $carrera->programa->nombre }}">{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                        <option value="GENERADO POR MATERIA">GENERADO POR MATERIA</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-danger" id="delete-carrera-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Carrera"><i class="ri-close-line"></i></button>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <select class="selectpicker form-control" id="filtro_docente" data-live-search="true">
                                        <option value="" selected disabled>Filtrar por Docente...</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{$docente->id}}" @if ($filtro_docente == $docente->id) selected @endif data-subtext="{{ $docente->numero_documento }}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-danger" id="delete-docente-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Docente"><i class="ri-close-line"></i></button>
                                </div>
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
                            <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_inscriptos" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Inscriptos...</option>
                                            <option value="0-10" @if ($filtro_inscriptos == '0-10') selected @endif>0 - 10</option>
                                            <option value="11-20" @if ($filtro_inscriptos == '11-20') selected @endif>11 - 20</option>
                                            <option value="21-30" @if ($filtro_inscriptos == '21-30') selected @endif>21 - 30</option>
                                            <option value="31-40" @if ($filtro_inscriptos == '31-40') selected @endif>31 - 40</option>
                                            <option value="41-50" @if ($filtro_inscriptos == '41-50') selected @endif>41 - 50</option>
                                            <option value="51" @if ($filtro_inscriptos == '51') selected @endif>+ 51</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-inscriptos-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Inscriptos"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                        </div>
                        <div class="table-responsive table-card mt-3 mb-1">
                            <table class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th class="text-center">Horas</th>
                                        <th>Materia</th>
                                        <th>Docente</th>
                                        <th>Semestre</th>
                                        <th>Modalidad</th>
                                        <th>Carrera</th>
                                        <th class="text-center">Cant. Alumnos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="form-check-all">
                                    @forelse ($clases_materias as $clase)
                                        <tr>
                                            <td>{{\Carbon\Carbon::parse($clase->fecha_hora)->format('d/m/Y H:i')}}</td>
                                            <td class="text-center">{{$clase->horas_desarrollo}}</td>
                                            <td>{{$clase->materia->nombre_fantasia}}</td>
                                            <td>{{$clase->docente->primer_nombre}} {{$clase->docente->primer_apellido}}</td>
                                            <td>{{$clase->semestre->nombre}}</td>
                                            <td>{{$clase->modalidad->nombre}}</td>
                                            @if ($clase->carrera_id)
                                                <td>{{$clase->carrera->nombre_fantasia}}</td>
                                            @else
                                                <td>GENERADO POR MATERIA</td>
                                            @endif
                                            <td class="text-center">{{$clase->alumnoAsistencias->count()}}</td>
                                            <td>
                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('clases_materias.show', $clase->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                @if ($clase->alumnoAsistencias->count() == 0)
                                                    @can('crear_alumnos_asistencias_materias_semestres')
                                                        <a type="button" class="btn btn-sm btn-success" href="{{route('alumnos_asistencias.create', ['materia' => $clase->materia_id, 'semestre' => $clase->semestre_id, 'docente' => $clase->docente_id])}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Agregar Asistencias"><i class="ri-add-fill"></i></a>
                                                    @endcan
                                                @endif
                                                @can('eliminar_materias_clases')
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$clase->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                <h5 class="mt-2">Sin resultados.</h5>
                                                <p class="text-muted mb-0">No pudimos encontrar ningúna clase según tus parámetros de búsqueda.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            @if ($clases_materias->hasPages())
                                <nav class="justify-content-lg-end">
                                    {{ $clases_materias->links('pagination::bootstrap-5') }}
                                </nav>
                            @else
                                <p class="small text-muted">
                                    Mostrando <span class="fw-semibold">{{$clases_materias->count()}}</span>
                                    a <span class="fw-semibold">{{$clases_materias->count()}}</span>
                                    de <span class="fw-semibold">{{$clases_materias->count()}}</span>
                                    resultados
                                </p>
                            @endif
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
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('clases_materias.scripts.index-scripts')
    @endsection
@endcan
