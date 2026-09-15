@can('ver_asistencias_alumnos')
    @extends('layouts.master')
    @section('title') Ver Asistencias @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Asistencias @endslot
            @slot('title') Ver Asistencias  @endslot
        @endcomponent

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h4 class="card-title mb-0">Visualizar asistencias</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <input type="text" class="form-control" id="sexo" value="{{$alumno->sexo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="text" class="form-control" id="fecha_nacimiento" value="{{\Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="edad">Edad</label>
                                    <input type="text" class="form-control" id="edad" value="{{\Carbon\Carbon::createFromDate($alumno->fecha_nacimiento)->age}} años" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" value="{{$alumno->telefono}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular</label>
                                    <input type="text" class="form-control" id="celular" value="{{$alumno->celular}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_personal">Correo Personal</label>
                                    <input type="text" class="form-control" id="email_personal" value="{{$alumno->email_personal}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_institucional">Correo Institucional</label>
                                    <input type="text" class="form-control" id="email_institucional" value="{{$alumno->email_institucional}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Asistencias</h4>
                                </div>
                                <div class="card-body">
                                    <div id="asistencias-list">
                                        <div class="row g-4 mb-3">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <div class="input-group">
                                                            <select class="selectpicker form-control" id="filtro_materia" data-live-search="true">
                                                                <option value="" selected disabled>Filtrar por Materia...</option>
                                                                @foreach ($materias as $materia)
                                                                    <option value="{{$materia->nombre_fantasia}}">{{$materia->nombre_fantasia}}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" id="delete-materia-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Materia"><i class="ri-close-line"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="input-group">
                                                            <select class="selectpicker form-control" id="filtro_periodo" data-live-search="true">
                                                                <option value="" selected disabled>Filtrar por Período...</option>
                                                                @foreach ($semestres as $semestre)
                                                                    <option value="{{$semestre->nombre}}">{{$semestre->nombre}}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" id="delete-periodo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="input-group">
                                                            <select class="selectpicker form-control" id="filtro_asistencia" data-live-search="true">
                                                                <option value="" selected disabled>Filtrar por Asistencia...</option>
                                                                <option value="10">0-10 %</option>
                                                                <option value="20">11-20 %</option>
                                                                <option value="30">21-30 %</option>
                                                                <option value="40">31-40 %</option>
                                                                <option value="50">41-50 %</option>
                                                                <option value="60">51-60 %</option>
                                                                <option value="70">61-70 %</option>
                                                                <option value="80">71-80 %</option>
                                                                <option value="90">81-90 %</option>
                                                                <option value="100">91-100 %</option>
                                                            </select>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" id="delete-asistencia-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Asistencia"><i class="ri-close-line"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="d-flex justify-content-sm-end">
                                                            <div class="search-box ms-2">
                                                                <input type="text" class="form-control search" placeholder="Buscar...">
                                                                <i class="ri-search-line search-icon"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap text-center" id="asistencias-list">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort" data-sort="materia">Materia</th>
                                                        <th class="sort" data-sort="total_clases">Total de Clases</th>
                                                        <th class="sort" data-sort="horas_desarrollo">Horas Totales</th>
                                                        <th class="sort" data-sort="total_asistido">Clases Asistidas</th>
                                                        <th class="sort" data-sort="horas_asistencia">Horas Asistidas</th>
                                                        <th class="sort" data-sort="porcentaje">% Asistencia</th>
                                                        <th class="sort" data-sort="periodo">Semestre</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                    @foreach ($asistencias as $key => $asistencia)
                                                        <tr>
                                                            <td class="materia">{{$asistencia->materia->nombre_fantasia}}</td>
                                                            <td class="total_clases">{{$asistencia->total_clases}} @if ($asistencia->total_clases == 1) clase @else clases @endif</td>
                                                            <td class="horas_desarrollo">{{$asistencia->horas_desarrollo}} @if ($asistencia->horas_desarrollo == 1) hora @else horas @endif</td>
                                                            <td class="total_asistido">{{$asistencia->total_asistido}} @if ($asistencia->total_asistido == 1) clase @else clases @endif</td>
                                                            <td class="horas_asistencia">{{$asistencia->horas_asistidas}} @if ($asistencia->horas_asistidas == 1) hora @else horas @endif</td>
                                                            <td>{{$asistencia->porcentaje}} %</td>
                                                            <td class="d-none porcentaje">{{$asistencia->porcentaje}}</td>
                                                            <td class="periodo">{{$asistencia->claseMateria->semestre->nombre}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="noresults" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="pagination-wrap hstack gap-2">
                                                <a class="page-item pagination-prev disabled"><</a>
                                                <ul class="pagination listjs-pagination mb-0"></ul>
                                                <a class="page-item pagination-next">></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('alumnos.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('alumnos.scripts.show_asistencias-scripts')
    @endsection
@endcan
