@can('ver_noticias_avisos')
    @extends('layouts.master')
    @section('title') Noticias y Avisos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Noticias y Avisos @endslot
        @endcomponent

        @include('noticias_avisos.scripts.messages-scripts')
        @include('noticias_avisos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Noticias y Avisos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="noticias_avisos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-1">
                                    @can('crear_noticias_avisos')
                                        <a class="btn btn-success" href="{{route('noticias_avisos.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_tipo">
                                            <option value="" selected disabled>Filtrar por Tipo...</option>
                                            <option value="NOTICIA">NOTICIAS</option>
                                            <option value="AVISO">AVISOS</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-tipo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Tipo"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap text-center" id="noticias_avisos-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="fecha">Fecha Publicación</th>
                                            <th class="sort" data-sort="tipo">Tipo</th>
                                            <th class="sort" data-sort="titulo">Título</th>
                                            <th>Portada</th>
                                            <th>Destacado</th>
                                            <th class="sort" data-sort="creado">Creado Por</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($noticias_avisos as $noticia_aviso)
                                            <tr>
                                                <td class="fecha">{{\Carbon\Carbon::parse($noticia_aviso->fecha_hora_publicacion)->format('d/m/Y H:i:s')}}</td>
                                                @if ($noticia_aviso->tipo == 'NO')
                                                    <td class="tipo">NOTICIA</td>
                                                @else
                                                    <td class="tipo">AVISO</td>
                                                @endif
                                                <td class="titulo">{{$noticia_aviso->titulo}}</td>
                                                <td><a href="{{asset($noticia_aviso->portada)}}" target="_blank"><img src="{{asset($noticia_aviso->portada)}}" style="width: 80px; height: 80px;"></a></td>
                                                <td>
                                                    @if ($noticia_aviso->destacado)
                                                        <h2 class="text-warning"><i class="ri-star-fill"></i></h2>
                                                    @endif
                                                </td>
                                                <td class="creado">{{$noticia_aviso->cargadoPor->name}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($noticia_aviso->estado == 'PU')
                                                            bg-success-subtle text-success text-uppercase"> Publicado
                                                        @elseif ($noticia_aviso->estado == 'ES')
                                                            bg-warning-subtle text-warning text-uppercase"> En Espera
                                                        @elseif ($noticia_aviso->estado == 'NP')
                                                            bg-danger-subtle text-danger text-uppercase"> No Publicado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('noticias_avisos.show', $noticia_aviso->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_noticias_avisos')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('noticias_avisos.edit', $noticia_aviso->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($noticia_aviso->estado == 'PU' || $noticia_aviso->estado == 'ES')
                                                        @can('inactivar_noticias_avisos')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$noticia_aviso->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="No Publicar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($noticia_aviso->estado == 'NP' || $noticia_aviso->estado == 'ES')
                                                        @can('activar_noticias_avisos')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$noticia_aviso->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Publicar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_noticias_avisos')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$noticia_aviso->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna noticia/aviso según tus parámetros de búsqueda.</p>
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
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('noticias_avisos.scripts.index-scripts')
    @endsection
@endcan
