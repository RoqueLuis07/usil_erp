@can('ver_mallas_espejo')
    @extends('layouts.master')
    @section('title') Ver Malla Espejo @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Mallas Espejo @endslot
            @slot('title') Ver Malla Espejo  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar malla espejo</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="malla_paraguay">Malla Paraguay</label>
                                    <input type="text" class="form-control" id="malla_paraguay" value="{{$malla_espejo->mallaParaguay->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="malla_siu">Malla SIU</label>
                                    <input type="text" class="form-control" id="malla_siu" value="{{$malla_espejo->mallaSiu->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_materias">Cant. de Materias</label>
                                        <input class="form-control text-center" type="text" id="cantidad_materias" value="{{number_format($malla_espejo->mallaEspejoDetalles->count(), 0, ',', '.')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Materias de la Malla Espejo</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($malla_espejo->mallaEspejoDetalles as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-6 col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="materia_paraguay">Materia Paraguay</label> @endif
                                                    <input type="text" class="form-control" id="materia_paraguay" value="{{$detalle->materiaParaguay->nombre_fantasia}}" readonly>
                                                </div>
                                                <div class="col-5 col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="materia_siu">Materia SIU</label> @endif
                                                    <input type="text" class="form-control" id="materia_siu" value="{{$detalle->materiaSiu->nombre_fantasia}}" readonly>
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
                            {{$malla_espejo->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($malla_espejo->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($malla_espejo->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$malla_espejo->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($malla_espejo->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('mallas_espejos.index')}}">Volver</a>
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
