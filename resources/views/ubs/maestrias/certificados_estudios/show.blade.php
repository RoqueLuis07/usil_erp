@can('ver_certificados_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Ver Certificado de Estudios @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Certificado de Estudios @endslot
            @slot('title') Ver Certificado de Estudios  @endslot
        @endcomponent

        @include('ubs.maestrias.certificados_estudios.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar certificado de estudios</h4>
                            </div>
                            @can('generar_certificados_maestrias_ubs')
                                <div class="col-lg-6 text-end">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-info me-2" id="btn-generar" data-url="{{route('certificados_estudios_ubs.generar', ['maestria' => $maestria_id, 'alumno' => $alumno->id])}}">Generar Certificado de Estudios</button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <input type="text" class="form-control" id="sexo" value="{{$alumno->sexo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="text" class="form-control" id="fecha_nacimiento" value="{{\Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="edad">Edad</label>
                                    <input type="text" class="form-control" id="edad" value="{{\Carbon\Carbon::createFromDate($alumno->fecha_nacimiento)->age}} años" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" value="{{$alumno->telefono}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular</label>
                                    <input type="text" class="form-control" id="celular" value="{{$alumno->celular}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_personal">Correo Personal</label>
                                    <input type="text" class="form-control" id="email_personal" value="{{$alumno->email_personal}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_institucional">Correo Institucional</label>
                                    <input type="text" class="form-control" id="email_institucional" value="{{$alumno->email_institucional}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Módulos</h4>
                                </div>
                                <div class="card-body">
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-12">
                                                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-info" id="acordeon">
                                                        @forelse ($semestres as $key => $semestre)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header">
                                                                    <button type="button" class="accordion-button collapsed semestre" data-bs-toggle="collapse" data-bs-target="#acordeon-{{$key}}" aria-expanded="false" aria-controls="acordeon-{{$key}}" id="semestre-{{$key}}" data-id="{{$key}}">SEMESTRE {{$semestre['numero']}}</button>
                                                                </h2>
                                                                <div class="accordion-collapse collapse" id="acordeon-{{$key}}" aria-labelledby="{{$key}}-acordeon" data-bs-parent="#acordeon">
                                                                    <div class="accordion-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <div class="table-responsive table-card mt-3 mb-1">
                                                                                    <table class="table align-middle table-nowrap text-center" id="semestreTabla-{{$key}}">
                                                                                        <thead class="table-light">
                                                                                            <tr>
                                                                                                <th>Asignatura</th>
                                                                                                <th>N° Acta</th>
                                                                                                <th>Fecha</th>
                                                                                                <th>Calificación</th>
                                                                                                <th>Periodo de Examen</th>
                                                                                                <th>Carga Horaria</th>
                                                                                                <th>Modalidad</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($alumno_notas as $nota)
                                                                                                @if ($nota->semestre_modulo == $semestre['numero'])
                                                                                                    <tr>
                                                                                                        <td>{{$nota->modulo->nombre_real}}</td>
                                                                                                        <td>{{$nota->numero_acta}}</td>
                                                                                                        <td>{{\Carbon\Carbon::parse($nota->fecha)->format('d/m/Y')}}</td>
                                                                                                        <td>{{$nota->calificacion}} ({{$nota->calificacion_letras}})</td>
                                                                                                        <td class="d-none calificacion">{{$nota->calificacion}}</td>
                                                                                                        <td>{{$nota->evaluacion}}</td>
                                                                                                        <td class="carga_horaria">{{$nota->modulo->carga_horaria}}</td>
                                                                                                        <td>{{$nota->curso->modalidad->nombre}}</td>
                                                                                                    </tr>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                        <tfoot class="table-light">
                                                                                            <tr>
                                                                                                <th style="text-align: left;">Promedio Académico</th>
                                                                                                <th colspan="6">{{number_format($semestre['promedio'], 2, ',', '.')}}</th>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <th style="text-align: left;">Carga Horaria Total</th>
                                                                                                <th colspan="6">{{$semestre['carga_horaria']}}</th>
                                                                                            </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="row d-flex flex-wrap justify-content-center">
                                                                <div class="col-lg-12 col-sm-12 mb-2 text-center">
                                                                    <p>El alumno no cuenta con módulos cursados.</p>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                        @if ($tesis)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header">
                                                                    <button type="button" class="accordion-button collapsed semestre" data-bs-toggle="collapse" data-bs-target="#acordeon-tesis" aria-expanded="false" aria-controls="acordeon-tesis" id="tesis" data-id="tesis">TESIS</button>
                                                                </h2>
                                                                <div class="accordion-collapse collapse" id="acordeon-tesis" aria-labelledby="tesis-acordeon" data-bs-parent="#acordeon">
                                                                    <div class="accordion-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <div class="table-responsive table-card mt-3 mb-1">
                                                                                    <table class="table align-middle table-nowrap text-center" id="semestreTabla-tesis">
                                                                                        <thead class="table-light">
                                                                                            <tr>
                                                                                                <th>Tema</th>
                                                                                                <th>Fecha Defensa</th>
                                                                                                <th>Calificación</th>
                                                                                                <th>Estado</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <td>{{$tesis->tema}}</td>
                                                                                            <td>{{\Carbon\Carbon::parse($tesis->fechaDefensa->fecha)->format('d/m/Y')}} {{$tesis->fechaDefensa->hora}}</td>
                                                                                            <td>{{number_format($tesis->calificacion, 2, ',', '.')}}</td>
                                                                                            <td>
                                                                                                @if ($tesis->estado == 'EN')
                                                                                                    <span class="text-success fw-bold">APROBADO</span>
                                                                                                @elseif ($tesis->estado == 'RE')
                                                                                                    <span class="text-danger fw-bold">REPROBADO</span>
                                                                                                @endif
                                                                                            </td>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('ubs.maestrias.certificados_estudios.scripts.show-scripts')
    @endsection
@endcan
