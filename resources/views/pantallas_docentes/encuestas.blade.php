@can('ver_encuestas_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Encuestas @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="row justify-content-center">
                    @forelse ($encuestas as $encuesta)
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                        <i class="bi bi-file-bar-graph"></i>
                                    </div>
                                </div>
                                <h4 class="card-title">{{$encuesta->nombre}}</h4>
                                <a type="button" class="btn btn-info" href="{{asset($encuesta->url_forms)}}" target="_blank">Completar</a>
                            </div>
                        </div>
                    @empty
                    <div class="col-lg-4">
                        <div class="card card-body text-center">
                            <h4 class="card-title">Actualmente no hay encuestas disponibles para su respuesta</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <a type="button" class="btn btn-info" href="{{route('pantallas_docentes.index', Auth::id())}}">Volver a Inicio</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
