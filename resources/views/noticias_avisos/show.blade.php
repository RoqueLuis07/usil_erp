@can('ver_noticias_avisos')
    @extends('layouts.master')
    @section('title') Ver @if ($noticia_aviso->tipo == 'NO') Noticia @else Aviso @endif @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Noticias y Avisos @endslot
            @slot('title') Ver @if ($noticia_aviso->tipo == 'NO') Noticia @else Aviso @endif @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar @if ($noticia_aviso->tipo == 'NO') noticia @else aviso @endif</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <div class="row">
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="fecha_hora_publicacion">Fecha y Hora</label>
                                            <input type="text" class="form-control text-center" id="fecha_hora_publicacion" value="{{Carbon\Carbon::parse($noticia_aviso->fecha_hora_publicacion)->format('d/m/y H:i')}}" readonly>
                                        </div>
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label" for="titulo">Título</label>
                                            <input type="text" class="form-control" id="titulo" value="{{$noticia_aviso->titulo}}" readonly>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            <div>
                                                <label class="form-label">Tipo</label>
                                            </div>
                                            <div class="btn-group" role="group">
                                                @if ($noticia_aviso->tipo == 'NO')
                                                    <input type="radio" class="btn-check tipo" id="tipo" checked>
                                                    <label class="btn btn-outline-warning" for="tipo">Noticia</label>
                                                @else
                                                    <input type="radio" class="btn-check tipo" id="tipo" name="tipo" checked>
                                                    <label class="btn btn-outline-success" for="tipo">Aviso</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            <div>
                                                <label class="form-label">Destacado</label>
                                            </div>
                                            <div class="btn-group" role="group">
                                                @if ($noticia_aviso->destacado)
                                                    <input type="radio" class="btn-check destacado" id="destacado" checked>
                                                    <label class="btn btn-outline-success" for="destacado">Si</label>
                                                @else
                                                    <input type="radio" class="btn-check destacado" id="destacado" checked>
                                                    <label class="btn btn-outline-danger" for="destacado">No</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="descripcion">Descripción</label>
                                            <textarea class="form-control" id="descripcion" cols="30" rows="10" readonly>{{$noticia_aviso->descripcion}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3 text-center mt-5">
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label" for="vista-imagen">Portada</label>
                                        <div class="text-center mb-3">
                                            <img src="@if ($noticia_aviso->portada) {{asset($noticia_aviso->portada)}} @else {{asset('storage/no_image.png')}} @endif" alt="Imagen" id="vista-imagen" style="width: 200px; height:200px;">
                                        </div>
                                        @can('eliminar_portadas_noticias_avisos')
                                        @if ($noticia_aviso->portada != 'storage/no_image.png')
                                            <button type="button" class="btn btn-danger" id="eliminar-portada" data-id="{{$noticia_aviso->id}}" data-url="{{route('noticias_avisos.destroy_portada', $noticia_aviso->id)}}">Eliminar Portada</button>
                                        @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('noticias_avisos.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/ckeditor.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/translations/es.min.js"></script>
        @include('noticias_avisos.scripts.show-scripts')
    @endsection
@endcan
