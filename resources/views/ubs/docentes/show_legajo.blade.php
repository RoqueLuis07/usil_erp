@can('ver_legajos_docentes_ubs')
    @extends('layouts.master')
    @section('title') Ver Legajo @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            @media screen and (max-width: 600px) {
                #div-subir {
                    margin-left: 2.6em;
                    margin-top: -0.5em;
                }
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Docentes @endslot
            @slot('title') Ver Legajo @endslot
        @endcomponent

        @include('ubs.docentes.scripts.messages-scripts')
        @include('ubs.docentes.modals.show-legajo-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-0">Visualizar legajo del docente</h4>
                        </div>
                        <div class="col-lg-6 text-end" id="div-subir" style="margin-bottom: -5em">
                            @can('subir_legajos_docentes_ubs')
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#subirLegajoModal" id="subir-legajo-btn">Subir Archivos</button>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label class="form-label" for="docente">Docente</label>
                                <input type="text" class="form-control" id="docente" value="{{$docente->primer_nombre}} {{$docente->primer_apellido}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="numero_documento">N° de Documento</label>
                                <input type="text" class="form-control" id="numero_documento" value="{{$docente->numero_documento}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="sexo">Sexo</label>
                                <input type="text" class="form-control" id="sexo" value="{{$docente->sexo->nombre}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                <input type="text" class="form-control" id="fecha_nacimiento" value="{{\Carbon\Carbon::parse($docente->fecha_nacimiento)->format('d/m/Y')}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="edad">Edad</label>
                                <input type="text" class="form-control" id="edad" value="{{\Carbon\Carbon::createFromDate($docente->fecha_nacimiento)->age}} años" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="telefono">N° de Teléfono</label>
                                <input type="text" class="form-control" id="telefono" value="{{$docente->telefono}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="celular">N° de Celular</label>
                                <input type="text" class="form-control" id="celular" value="{{$docente->celular}}" readonly>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label" for="email_personal">Correo Personal</label>
                                <input type="text" class="form-control" id="email_personal" value="{{$docente->email_personal}}" readonly>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label" for="email_institucional">Correo Institucional</label>
                                <input type="text" class="form-control" id="email_institucional" value="{{$docente->email_institucional}}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap justify-content-between">
                                <div class="col-lg-8">
                                    <h4 class="card-title mb-0">Legajo</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <div class="row justify-content-center">
                                            @forelse ($legajos as $legajo)
                                            <div class="col-lg-2 col-xxl-2">
                                                <div class="card card-body text-center">
                                                    <div class="mx-auto mb-3">
                                                        @if ($legajo->extension == 'pdf')
                                                            <img src="{{asset('storage/pdf.png')}}" alt="pdf_icon" style="width:50px; heigth:50px">
                                                        @else
                                                            <img src="{{asset('storage/img.png')}}" alt="image_icon" style="width:50px; heigth:50px">
                                                        @endif
                                                        @can('eliminar_legajos_docentes_ubs')
                                                            <button type="button" class="btn btn-danger position-absolute translate-middle badge bg-danger delete-legajo-btn" data-id="{{$legajo->id}}" data-tipo="{{$legajo->tipoLegajo->nombre}}">Eliminar</button>
                                                            <form action="{{route('docentes_ubs.eliminar_legajo', $legajo->id)}}" method="delete" id="delete-legajo-form-{{$legajo->id}}">
                                                                @csrf
                                                            </form>
                                                        @endcan
                                                    </div>
                                                    <h4 class="card-title">{{Str::title($legajo->tipoLegajo->nombre)}}</h4>
                                                    <a type="button" class="btn btn-info" href="{{asset($legajo->url_ubicacion)}}" target="_blank">Visualizar</a>
                                                </div>
                                            </div>
                                            @empty
                                                <div class="col-lg-12 mb-3 text-center">
                                                    <p>El docente no cuenta con ningún archivo subido en su legajo cargado.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-end mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('docentes_ubs.index')}}">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('ubs.docentes.scripts.show-legajo-scripts')
    @endsection
@endcan
