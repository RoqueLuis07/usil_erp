@can('ver maestrias_ubs')
    @extends('layouts.master')
    @section('title') Ver Maestría @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Maestrías @endslot
            @slot('title') Ver Maestría  @endslot
        @endcomponent

        @include('ubs.maestrias.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar maestría</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="nombre_fantasia">Nombre Fantasía</label>
                                                <input type="text" class="form-control" id="nombre_fantasia" value="{{$maestria->nombre_fantasia}}" readonly>
                                            </div>
                                            <div class="col-lg-3 mb-3">
                                                <label class="form-label" for="nombre_real">Nombre Real</label>
                                                <input type="text" class="form-control" id="nombre_real" value="{{$maestria->nombre_real}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="codigo">Código</label>
                                                <input type="text" class="form-control" id="codigo" value="{{$maestria->codigo}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="programa">Programa</label>
                                                <input type="text" class="form-control" id="programa" value="{{$maestria->programa->nombre}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="facultad">Facultad</label>
                                                <input type="text" class="form-control" id="facultad" value="{{$maestria->facultad->nombre}}" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="tipo_curso">Tipo</label>
                                                <input type="text" class="form-control" id="tipo_curso" value="{{$maestria->tipoCurso->nombre}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="modalidad">Modalidad</label>
                                                <input type="text" class="form-control" id="modalidad" value="{{$maestria->modalidad->nombre}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="fecha_apertura">Fecha de Apertura</label>
                                                <input type="text" class="form-control text-center" id="fecha_apertura" value="{{Carbon\Carbon::parse($maestria->fecha_apertura)->format('d/m/Y')}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="fecha_fin">Fecha de Fin</label>
                                                <input type="text" class="form-control text-center" id="fecha_fin" value="{{Carbon\Carbon::parse($maestria->fecha_fin)->format('d/m/Y')}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="cantidad_horas">Cantidad de Horas</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-center" id="cantidad_horas" value="{{number_format($maestria->cantidad_horas, 0, ',', '.')}}" readonly>
                                                    <span class="input-group-text">horas</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="duracion">Duración</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-center" id="duracion" value="{{number_format($maestria->duracion, 0, ',', '.')}}" readonly>
                                                    <span class="input-group-text">años</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="cantidad_creditos">Cantidad de Créditos</label>
                                                <input type="text" class="form-control text-center" id="cantidad_creditos" value="{{$maestria->cantidad_creditos}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="llamado">N° de Llamado</label>
                                                <input type="text" class="form-control text-center" id="llamado" value="{{$maestria->llamado}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="numero_ley">N° de Ley</label>
                                                <input type="text" class="form-control" id="numero_ley" value="{{$maestria->numero_ley}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="numero_acta">N° de Acta</label>
                                                <input type="text" class="form-control" id="numero_acta" value="{{$maestria->numero_acta}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="numero_resolucion_cones">N° de Res. del CONES</label>
                                                <input type="text" class="form-control" id="numero_resolucion_cones" value="{{$maestria->numero_resolucion_cones}}" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                <div>
                                                    <label class="form-label" for="evaluacion">Tiene Evaluación ?</label>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check evaluacion1" id="evaluacion1" name="evaluacion" value="false" @if (old('evaluacion') == 'false' || $maestria->evaluacion == false) checked @endif disabled>
                                                    <label class="btn btn-outline-danger" for="evaluacion1">No</label>
                                                    <input type="radio" class="btn-check evaluacion2" id="evaluacion2" name="evaluacion" value="true" @if (old('evaluacion') == 'true' || $maestria->evaluacion == true) checked @endif disabled>
                                                    <label class="btn btn-outline-success" for="evaluacion2">Si</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row text-center">
                                            <label class="form-label" for="vista-imagen">Cronograma</label>
                                            <div class="text-center">
                                                @if ($maestria->url_cronograma)
                                                    <a href="{{asset($maestria->url_cronograma)}}" target="_blank"><img src="{{asset($maestria->url_cronograma)}}" alt="Imagen" id="vista-imagen" style="width: 200px; height:200px;"></a>
                                                @else
                                                    <img src="{{asset('storage/no_image.png')}}" alt="Imagen" id="vista-imagen" style="width: 200px; height:200px;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Precios</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-lg-2">
                                            <label class="form-label" for="precio_contado">Contado</label>
                                            <input type="text" class="form-control text-center" id="precio_contado" value="{{number_format($maestria->precios->precio_contado, 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label" for="cantidad_cuotas">Cant. Cuotas</label>
                                            <input type="text" class="form-control text-center" id="cantidad_cuotas" value="{{number_format($maestria->precios->cantidad_cuotas, 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label" for="precio_cuota">Cuota</label>
                                            <input type="text" class="form-control text-center" id="precio_cuota" value="{{number_format($maestria->precios->precio_cuota, 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label" for="fecha_inicio_vencimiento_cuota">Fecha 1er Venc.</label>
                                            <input type="text" class="form-control text-center" id="fecha_inicio_vencimiento_cuota" value="{{Carbon\Carbon::parse($maestria->precios->fecha_inicio_vencimiento_cuota, 0, ',', '.')->format('d/m/Y')}}" readonly>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label" for="dia_vencimiento_cuota">Día Venc.</label>
                                            <input type="text" class="form-control text-center" id="dia_vencimiento_cuota" value="{{number_format($maestria->precios->dia_vencimiento_cuota, 0, ',', '.')}}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-2">
                                            <label class="form-label" for="precio_defensa">Defensa</label>
                                            <input type="text" class="form-control text-center" id="precio_defensa" value="{{number_format($maestria->precios->precio_defensa, 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label" for="precio_titulo">Titulación</label>
                                            <input type="text" class="form-control text-center" id="precio_titulo" value="{{number_format($maestria->precios->precio_titulo, 0, ',', '.')}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Módulos del Maestría</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-lg-12">
                                            @if ($maestria->modulos->count() > 0)
                                                <ol>
                                                    @foreach ($maestria->modulos as $detalle)
                                                        <li><a href="#alumnosModuloModal-{{$detalle->id}}" data-bs-toggle="modal" data-bs-target="#alumnosModuloModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Alumnos del Módulo">{{$detalle->modulo->nombre_fantasia}}</a> - <small class="text-muted">{{$detalle->docente->primer_nombre}} {{$detalle->docente->primer_apellido}}</small></li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <p>La maestría no cuenta con módulos asignados.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="mb-2">
                                <div class="row d-flex flex-wrap justify-content-center">
                                    <div class="col-lg-12">
                                        <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-info" id="acordeon">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acordeon-1" aria-expanded="false" aria-controls="acordeon-1">Lista de Alumnos</button>
                                                </h2>
                                                <div class="accordion-collapse collapse" id="acordeon-1" aria-labelledby="acordeon" data-bs-parent="#acordeon">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-lg-12 mb-3">
                                                                @if ($maestria->inscripciones->count() > 0)
                                                                    <div id="inscripciones-list">
                                                                        <div class="row mb-3">
                                                                            <div class="col-lg-4">
                                                                                <div class="d-flex justify-content-sm-start">
                                                                                    <div class="search-box ms-2">
                                                                                        <input type="text" class="form-control search" placeholder="Buscar...">
                                                                                        <i class="ri-search-line search-icon"></i>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-8">
                                                                                <div class="d-flex justify-content-end">
                                                                                    <div class="pagination-wrap hstack gap-2">
                                                                                        <a class="page-item pagination-prev disabled"><</a>
                                                                                        <ul class="pagination listjs-pagination mb-0"></ul>
                                                                                        <a class="page-item pagination-next">></a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="p-3">
                                                                            <div class="table-responsive table-card">
                                                                                <table class="table align-middle table-nowrap text-center" id="inscripciones-list">
                                                                                    <thead class="table-light">
                                                                                        <tr>
                                                                                            <th class="sort" data-sort="alumno">Alumno</th>
                                                                                            <th class="sort" data-sort="numero_documento">N° Documento</th>
                                                                                            <th>Estado</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody class="list form-check-all">
                                                                                        @foreach ($maestria->inscripciones as $inscripcion)
                                                                                            <tr>
                                                                                                <td class="alumno">{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</td>
                                                                                                <td class="numero_documento">{{$inscripcion->alumno->numero_documento}}</td>
                                                                                                <td>
                                                                                                    <span
                                                                                                        class="badge @if ($inscripcion->estado == 'AC')
                                                                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                                                                        @elseif ($inscripcion->estado == 'IN')
                                                                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                                                                        @endif
                                                                                                    </span>
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                <div class="noresults" style="display: none">
                                                                                    <div class="text-center">
                                                                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:50px;height:50px"></lord-icon>
                                                                                        <h5 class="mt-2">Sin resultados.</h5>
                                                                                        <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="col-lg-12 text-center">
                                                                        <h6>La maestría no cuenta con alumnos inscriptos actualmente.</h6>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$maestria->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($maestria->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($maestria->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$maestria->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($maestria->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('maestrias.index')}}">Volver</a>
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
        @include('ubs.maestrias.scripts.show-scripts')
    @endsection
@endcan
