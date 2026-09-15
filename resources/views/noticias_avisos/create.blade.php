@can('crear_noticias_avisos')
    @extends('layouts.master')
    @section('title') Agregar Noticia/Aviso @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
        <style>
            /* Chrome, Safari, Edge */
            input[type="number"]::-webkit-outer-spin-button,
            input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Firefox */
            input[type="number"] {
                -moz-appearance: textfield;
            }

            .ck-editor__editable_inline {
                min-height: 5cm;
                font-family: 'Open-sans', sans-serif;
                font-size: 14px;
                padding: 10px;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Noticias y Avisos @endslot
            @slot('title') Agregar Noticia/aviso  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('noticias_avisos.store')}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva noticia/aviso</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="fecha_hora_publicacion">Fecha y Hora <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="form-control form-control-icon flatpickr text-center @error('fecha_hora_publicacion') is-invalid @enderror" id="fecha_hora_publicacion" name="fecha_hora_publicacion" value="{{old('fecha_hora_publicacion')}}">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_hora_publicacion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="titulo">Título <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{old('titulo')}}">
                                    @error('titulo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label">Tipo <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('tipo') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check tipo1" id="tipo1" name="tipo" value="NO" @if (old('tipo') == 'NO') checked @endif>
                                        <label class="btn btn-outline-warning" for="tipo1">Noticia</label>
                                        <input type="radio" class="btn-check tipo2" id="tipo2" name="tipo" value="AV" @if (old('tipo') == 'AV') checked @endif>
                                        <label class="btn btn-outline-success" for="tipo2">Aviso</label>
                                    </div>
                                    @error('tipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label">Destacado ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('destacado') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check destacado1" id="destacado1" name="destacado" value="false" @if (old('destacado') == 'false' || old('destacado') == null) checked @endif>
                                        <label class="btn btn-outline-danger" for="destacado1">No</label>
                                        <input type="radio" class="btn-check destacado2" id="destacado2" name="destacado" value="true" @if (old('destacado') == 'true') checked @endif>
                                        <label class="btn btn-outline-success" for="destacado2">Si</label>
                                    </div>
                                    @error('destacado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3" id="div-portada">
                                    <label class="form-label" for="portada">Portada</label>
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('portada') is-invalid @enderror" id="portada" name="portada" accept="image/jpeg,image/png" onchange="readURL(this);">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-portada" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('portada')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <p class="text-muted">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="descripcion">Descripción <span class="text-danger">(*)</span></label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" cols="30" rows="10">{{old('descripcion')}}</textarea>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="vista-imagen">Visualización Previa</label>
                                    <div class="text-center">
                                        <img src="{{asset('storage/no_image.png')}}" alt="Imagen" id="vista-imagen" style="width: 200px; height:200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success me-2" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/ckeditor.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/translations/es.min.js"></script>
        @include('noticias_avisos.scripts.create-scripts')
    @endsection
@endcan
