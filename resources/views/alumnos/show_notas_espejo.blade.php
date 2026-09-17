@can('ver_notas_alumnos')
    @extends('layouts.master-academic')
    @section('title') Ver Notas Espejo @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Notas Espejo @endslot
            @slot('title') Ver Notas Espejo  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h4 class="card-title mb-0">Visualizar notas espejo</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
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
                                <div id="notas-list">
                                    <div class="card-header d-flex flex-wrap justify-content-between">
                                        <div class="col-lg-8">
                                            <h4 class="card-title mb-0">Notas</h4>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex justify-content-sm-end">
                                                <div class="search-box ms-2">
                                                    <input type="text" class="form-control search" placeholder="Buscar...">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap text-center" id="notas-list">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th colspan="3" style="border-right: 2px solid black">Malla Nacional ({{$alumno->carrera_paraguay}})</th>
                                                        <th colspan="3" style="border-right: 2px solid black">SIU ({{$alumno->carrera_siu}})</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th class="sort" data-sort="semestre_paraguay">Semestre</th>
                                                        <th class="sort" data-sort="materia_paraguay">Materia</th>
                                                        <th class="sort" data-sort="calificacion_paraguay" style="border-right: 2px solid black">Calificación</th>
                                                        <th class="sort" data-sort="semestre_siu">Semestre</th>
                                                        <th class="sort" data-sort="materia_siu">Materia</th>
                                                        <th class="sort" data-sort="calificacion_siu" style="border-right: 2px solid black">Calificación</th>
                                                        <th class="sort" data-sort="periodo">Periodo</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                    @foreach ($espejos as $espejo)
                                                        <tr>
                                                            <td class="semestre_paraguay">{{$espejo['semestre_materia_paraguay']}}</td>
                                                            <td class="materia_paraguay">{{$espejo['materia_paraguay']}}</td>
                                                            <td class="calificacion_paraguay" style="border-right: 2px solid black">{{$espejo['nota_paraguay']}}</td>
                                                            @php
                                                                $text_color = '';
                                                                if ($espejo['estado']) {
                                                                    $text_color = 'text-danger';
                                                                }
                                                            @endphp
                                                            <td class="semestre_siu {{$text_color}}">{{$espejo['semestre_materia_siu']}}</td>
                                                            <td class="materia_siu {{$text_color}}">{{$espejo['materia_siu']}}</td>
                                                            <td class="calificacion_siu {{$text_color}}" style="border-right: 2px solid black">{{$espejo['nota_siu']}}</td>
                                                            <td class="periodo">{{$espejo['periodo']}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3">Total de Materias: {{$cantidad_materias_paraguay}}</td>
                                                        <td colspan="3">Total de Materias: {{$cantidad_materias_siu}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <div class="noresults" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ninguna materia según tus parámetros de búsqueda.</p>
                                                </div>
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
        @include('alumnos.scripts.show_notas_espejo-scripts')
    @endsection
@endcan
