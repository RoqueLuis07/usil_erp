@can('ver_borradores_tesis_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Ver Borrador de T.F.G. @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_alumnos.scripts.messages-scripts')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Borrador de Trabajo Final de Grado</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{\Carbon\Carbon::parse($tema->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control text-center" id="carrera" value="{{$tema->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control text-center" id="programa" value="{{$tema->programa->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control text-center" id="tutor" value="{{$tema->tutor->primer_nombre}} {{$tema->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold
                                        @if ($tema->estado == 'PE' || $tema->estado == 'RE' || $tema->estado == 'RR')
                                            text-danger
                                        @elseif ($tema->estado == 'AT' || $tema->estado == 'EC' || $tema->estado == 'PC' || $tema->estado == 'BC' || $tema->estado == 'FE')
                                            text-warning
                                        @elseif ($tema->estado == 'AC' || $tema->estado == 'AA' || $tema->estado == 'AP' || $tema->estado == 'AB' || $tema->estado == 'PA' || $tema->estado == 'EN')
                                            text-success
                                        @endif"
                                        id="estado"
                                        @if ($tema->estado == 'PE')
                                            value="PENDIENTE"
                                        @elseif ($tema->estado == 'RE')
                                            value="RECHAZADO"
                                        @elseif ($tema->estado == 'AT')
                                            value="APROBADO POR TUTOR"
                                        @elseif ($tema->estado == 'AC')
                                            value="APROBADO POR COORDINACION"
                                        @elseif ($tema->estado == 'EC')
                                            value="ANTEPROYECTO EN CURSO"
                                        @elseif ($tema->estado == 'PC')
                                            value="PROYECTO EN CURSO"
                                        @elseif ($tema->estado == 'BC')
                                            value="BORRADOR EN CURSO"
                                        @elseif ($tema->estado == 'AA')
                                            value="ANTEPROYECTO APROBADO"
                                        @elseif ($tema->estado == 'AP')
                                            value="PROYECTO APROBADO"
                                        @elseif ($tema->estado == 'AB')
                                            value="BORRADOR APROBADO"
                                        @elseif ($tema->estado == 'PA')
                                            value="PAGADO"
                                        @elseif ($tema->estado == 'FE')
                                            value="DEFENSA"
                                        @elseif ($tema->estado == 'EN')
                                            value="APROBADO"
                                        @elseif ($tema->estado == 'RR')
                                            value="REPROBADO"
                                        @endif
                                    readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control text-center" id="tipo" value="{{$tema->tipo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="area">Área</label>
                                    <input type="text" class="form-control text-center" id="area" value="{{$tema->area->nombre}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="linea">Línea</label>
                                    <input type="text" class="form-control text-center" id="linea" value="{{$tema->linea->nombre}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control text-center" id="tema" value="{{$tema->tema}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="lugar_investigacion">Lugar de Investigación</label>
                                    <input type="text" class="form-control text-center" id="lugar_investigacion" value="{{$tema->lugar_investigacion}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Bloques</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle table-nowrap text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Bloque</th>
                                                    <th>Alumno</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Aprobación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                @foreach ($tema->borradores as $borrador)
                                                    <tr>
                                                        <td style="text-align: left!important">BLOQUE {{$borrador->bloque->numero}} | {{$borrador->bloque->nombre}}</td>
                                                        <td>{{$borrador->inscripcion->alumno->primer_nombre}} {{$borrador->inscripcion->alumno->primer_apellido}}</td>
                                                        <td>
                                                            <span
                                                                class="badge @if ($borrador->estado == 'PE')
                                                                    bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                                @elseif ($borrador->estado == 'EN')
                                                                    bg-warning-subtle text-warning text-uppercase"> Entregado
                                                                @elseif ($borrador->estado == 'CO')
                                                                    bg-warning-subtle text-warning text-uppercase"> Corregido
                                                                @elseif ($borrador->estado == 'AP')
                                                                    bg-success-subtle text-success text-uppercase"> Aprobado
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($borrador->fecha_aprobado_tutor)
                                                                {{\Carbon\Carbon::parse($borrador->fecha_aprobado_tutor)->format('d/m/Y')}}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @can('ver_entregas_borradores_tesis_alumnos_pantalla')
                                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('pantallas_alumnos.show_entregas_borradores_tesis', $borrador->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Entregas"><i class="ri-eye-fill"></i></a>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('pantallas_alumnos.borradores_tesis', Auth::id())}}">Volver</a>
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
        @include('pantallas_alumnos.tesis.borradores.scripts.show-scripts')
@endsection
@endcan
