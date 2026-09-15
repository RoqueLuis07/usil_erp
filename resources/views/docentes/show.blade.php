@can('ver_docentes')
    @extends('layouts.master')
    @section('title') Ver Docente @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Docentes @endslot
            @slot('title') Ver Docente  @endslot
        @endcomponent

        @include('docentes.scripts.messages-scripts')

        <div class="row">
            <form id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar docente</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nombre_completo">Nombre Completo</label>
                                    <input type="text" class="form-control" id="nombre_completo" value="{{$nombre_docente}}" readonly>
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
                                    <label class="form-label" for="nacionalidad">Nacionalidad</label>
                                    <input type="text" class="form-control" id="nacionalidad">
                                </div>
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
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" value="{{$docente->direccion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento">Departamento</label>
                                    <input type="text" class="form-control" id="departamento" value="{{$docente->departamento->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" value="{{$docente->ciudad->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="barrio">Barrio</label>
                                    <input type="text" class="form-control" id="barrio" value="{{$docente->barrio->nombre}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="usuario">Usuario Asignado</label>
                                    <input class="form-control" type="text" id="usuario" value="{{$docente->usuario->name}} - {{$docente->usuario->rol->name}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="ubs">Docente UBS</label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check ubs1" id="ubs1" name="ubs" value="false" @if (old('ubs') == 'false' || $docente->ubs == false) checked @endif disabled>
                                        <label class="btn btn-outline-danger" for="ubs1">No</label>
                                        <input type="radio" class="btn-check ubs2" id="ubs2" name="ubs" value="true" @if (old('ubs') == 'true' || $docente->ubs == true) checked @endif disabled>
                                        <label class="btn btn-outline-success" for="ubs2">Si</label>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="tutor_tesis">Tutor T.F.G.</label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check tutor_tesis1" id="tutor_tesis1" name="tutor_tesis" value="false" @if (old('tutor_tesis') == 'false' || $docente->tutor_tesis == false) checked @endif disabled>
                                        <label class="btn btn-outline-danger" for="tutor_tesis1">No</label>
                                        <input type="radio" class="btn-check tutor_tesis2" id="tutor_tesis2" name="tutor_tesis" value="true" @if (old('tutor_tesis') == 'true' || $docente->tutor_tesis == true) checked @endif disabled>
                                        <label class="btn btn-outline-success" for="tutor_tesis2">Si</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistFormacion" role="tab" aria-selected="true">Formación Educativa</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistFormacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="nivel_academico">Nivel Académico</label>
                                                    <input type="text" class="form-control" id="nivel_academico" value="{{$docente->nivelAcademico->nombre}}" readonly>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="area_conocimiento">Área de Conocimiento</label>
                                                    <input type="text" class="form-control" id="area_conocimiento" @if ($docente->area_conocimiento_id) value="{{$docente->areaConocimiento->nombre}} - {{ $docente->areaConocimiento->abreviatura }}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="institucion_educativa">Capcaticación Didáctica</label>
                                                    <div class="form-check form-check-success">
                                                        <input type="checkbox" class="form-check-input" @if ($docente->capacitacion_didactica == true) checked @endif disabled style="width:30px; height:30px; margin-left:40px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /Tab panes --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$docente->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($docente->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($docente->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$docente->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($docente->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('docentes.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        @include('docentes.scripts.show-scripts')
    @endsection
@endcan
