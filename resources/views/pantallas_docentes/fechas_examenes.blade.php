@can('ver_fechas_examenes_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Fechas de Exámenes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
                @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Mis Fechas de Exámenes</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            @forelse ($fechas_examenes as $fecha_examen)
                                                <div class="col-lg-3">
                                                    <div class="card card-body text-center">
                                                        <div class="avatar-sm mx-auto mb-3">
                                                            <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                                <i class="bi bi-calendar-fill"></i>
                                                            </div>
                                                        </div>
                                                        <h4 class="card-title">{{$fecha_examen->materia->nombre_fantasia}}</h4>
                                                        <table class="table text-start">
                                                            <tr>
                                                                <td><b>Parcial:</b></td>
                                                                <td class="text-end">{{Carbon\Carbon::parse($fecha_examen->fecha_examen_parcial)->format('d/m/Y')}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Ordinario:</b></td>
                                                                <td class="text-end">{{Carbon\Carbon::parse($fecha_examen->fecha_examen_ordinario)->format('d/m/Y')}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Complementario:</b></td>
                                                                <td class="text-end">{{Carbon\Carbon::parse($fecha_examen->fecha_examen_complementario)->format('d/m/Y')}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Extraordinario:</b></td>
                                                                <td class="text-end">{{Carbon\Carbon::parse($fecha_examen->fecha_examen_extraordinario)->format('d/m/Y')}}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-lg-12 text-center">
                                                    <p>No se encontraron fechas de exámenes asignadas.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @endsection
@endcan
