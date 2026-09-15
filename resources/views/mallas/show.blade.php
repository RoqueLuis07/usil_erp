@can('ver_mallas')
    @extends('layouts.master')
    @section('title') Ver Malla @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Mallas @endslot
            @slot('title') Ver Malla  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar malla</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @can('imprimir_mallas')
                                    <a type="button" class="btn btn-warning me-2" href="{{route('mallas.pdf', $malla->id)}}" target="_blank">Imprimir Malla</a>
                                @endcan
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$malla->carrera->programa->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$malla->carrera->nombre_fantasia}} - {{$malla->carrera->nombre_real}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tipo_malla">Tipo de Malla</label>
                                    <input type="text" class="form-control" id="tipo_malla" value="{{$malla->tipoMalla->nombre}}" readonly>
                                </div>
                                <div class="col-lg-5 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_materias">Cant. de Materias</label>
                                        <input class="form-control text-center" type="text" id="cantidad_materias" value="{{number_format($malla->mallaDetalles->count(), 0, ',', '.')}}" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="carga_horaria_total">Carga Horaria Total</label>
                                        <input class="form-control text-center" type="text" id="carga_horaria_total" value="{{number_format($malla->mallaDetalles->sum('carga_horaria'), 0, ',', '.')}}" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_creditos_total">Cant. Créditos Total</label>
                                        <input class="form-control text-center" type="text" id="cantidad_creditos_total" value="{{number_format($malla->mallaDetalles->sum('cantidad_creditos'), 0, ',', '.')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Materias de la Malla</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($malla->mallaDetalles as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="materia">Materia</label> @endif
                                                    <input type="text" class="form-control" id="materia" value="{{$detalle->materia->nombre_fantasia}} - {{$detalle->materia->nombre_real}}" readonly>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="semestre">Semestre</label> @endif
                                                    <input type="text" class="form-control text-center" id="semestre" value="{{number_format($detalle->semestre, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="carga_horaria">Horas</label> @endif
                                                    <input type="text" class="form-control text-center" id="carga_horaria" value="{{number_format($detalle->carga_horaria, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="cantidad_creditos">Créditos</label> @endif
                                                    <input type="text" class="form-control text-center" id="cantidad_creditos" value="{{number_format($detalle->cantidad_creditos, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="area_curricular">Área</label> @endif
                                                    <input type="text" class="form-control text-center" id="area_curricular" @if ($detalle->area_curricular == 'B') value="BÁSICO" @elseif ($detalle->area_curricular == 'C') value="COMPLEMENTARIO" @elseif ($detalle->area_curricular == 'P') value="PROFESIONAL" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <div><label class="form-label" for="doble_grado">Doble Grado</label></div> @endif
                                                    <div class="btn-group" role="group">
                                                        <input type="radio" class="btn-check doble_grado1-0" id="doble_grado" @if ($detalle->doble_grado == true) checked @endif disabled>
                                                        <label class="btn @if ($detalle->doble_grado == true) btn-outline-success @else btn-outline-danger @endif" for="doble_grado">@if ($detalle->doble_grado == true) Sí @else No @endif</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$malla->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($malla->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($malla->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$malla->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($malla->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('mallas.index')}}">Volver</a>
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
