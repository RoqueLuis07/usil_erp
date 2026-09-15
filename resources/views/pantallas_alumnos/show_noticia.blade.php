@can('ver_noticias_avisos_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Ver Noticia @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="card overflow-hidden">
                                <div class="bg-primary pb-0">
                                    <div class="card-body text-center py-4">
                                        <h4 class="fw-medium text-white mb-3">{{Str::title($noticia->titulo)}}</h4>
                                    </div>
                                    <div class="card-body py-2 bg-white bg-opacity-10">
                                        <ul class="list-unstyled mb-0 text-white-75 hstack gap-2 justify-content-between">
                                            <li><b>Creado por:</b> {{Str::title($noticia->cargadoPor->name)}}</li>
                                            <li>{{Carbon\Carbon::parse($noticia->fecha_hora_publicacion)->translatedFormat('j \d\e F \d\e\l Y')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body text-muted">
                                    <p>{!! $noticia->descripcion !!}</p>
                                    <p class="text-center"><img src="{{asset($noticia->portada)}}" alt="Portada" style="width: 300px;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
