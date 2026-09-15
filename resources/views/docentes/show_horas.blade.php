{{-- @can('ver_reportes_horas_docentes') --}}
    @extends('layouts.master')
    @section('title') Editar Horas Docente @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Docentes @endslot
            @slot('title') Editar Horas Docente @endslot
        @endcomponent

        @include('docentes.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('docentes.reporte_horas', $docente->id) }}" method="post" id="store-form" target="_blank">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar horas docentes</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha Inicio</label>
                                    <input type="text" class="form-control text-center" id="fecha_inicio" value="{{$fecha_inicio->format('d/m/Y')}}" readonly>
                                    <input type="hidden" name="fecha_inicio" value="{{ $fecha_inicio }}">
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha Fin</label>
                                    <input type="text" class="form-control text-center" id="fecha_fin" value="{{$fecha_fin->format('d/m/Y')}}" readonly>
                                    <input type="hidden" name="fecha_fin" value="{{ $fecha_fin }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{ $docente->primer_nombre }} {{ $docente->primer_apellido }}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="docente_documento">N° Documento</label>
                                    <input type="text" class="form-control" id="docente_documento" value="{{ $docente->numero_documento }}" readonly>
                                </div>
                                <div class="col-lg-7 mb-3">
                                    <div class="row d-flex justify-content-end">
                                        <div class="col-lg-3 text-center">
                                            <label class="form-label" for="monto_virtual">Monto Virtual</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control monto text-center @error('monto_virtual') is-invalid @enderror" id="monto_virtual" name="monto_virtual" value="{{ old('monto_virtual', $monto_virtual) }}">
                                                @error('monto_virtual')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 text-center">
                                            <label class="form-label" for="monto_teams">Monto Teams</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control monto text-center @error('monto_teams') is-invalid @enderror" id="monto_teams" name="monto_teams" value="{{ old('monto_teams', $monto_teams) }}">
                                                @error('monto_teams')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 text-center">
                                            <label class="form-label" for="monto_presencial">Monto Presencial</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control monto text-center @error('monto_presencial') is-invalid @enderror" id="monto_presencial" name="monto_presencial" value="{{ old('monto_presencial', $monto_presencial) }}">
                                                @error('monto_presencial')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="mes">Mes</label>
                                    <input type="text" class="form-control @error('mes') is-invalid @enderror" id="mes" name="mes" value="{{Str::upper($fecha_inicio->translatedFormat('F'))}} {{$semestre->nombre}}" readonly>
                                    @error('mes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" value="@foreach($carreras as $key => $carrera){{$carrera['carrera']}}@if(!$loop->last) /@endif @endforeach" readonly>
                                    @error('mes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control @error('programa') is-invalid @enderror" id="programa" name="programa" value="GRADO AMERICANO" readonly>
                                    @error('programa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap justify-content-between">
                                    <div class="col-lg-8">
                                        <h4 class="card-title mb-0">Asignaturas</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-lg-4 mb-2 text-center">
                                            <label class="form-label" for="materia">Asignatura</label>
                                        </div>
                                        <div class="col-lg-2 mb-2 text-center">
                                            <label class="form-label" for="horas_virtuales">Horas Virtuales</label>
                                        </div>
                                        <div class="col-lg-2 mb-2 text-center">
                                            <label class="form-label" for="horas_teams">Horas Presenciales (Teams)</label>
                                        </div>
                                        <div class="col-lg-2 mb-2 text-center">
                                            <label class="form-label" for="horas_presenciales">Horas Presenciales</label>
                                        </div>
                                        <div class="col-lg-2 mb-2 text-center">
                                            <label class="form-label" for="observaciones">Observaciones</label>
                                        </div>
                                    </div>
                                    @php
                                        $key = 0;
                                    @endphp
                                    @foreach ($materias_virtuales as $materia)
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-lg-4 mb-2 text-center">
                                                <input type="text" class="form-control text-center @error('detalles.' . $key . '.materia') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][materia]" value="{{ $materia['materia'] }}" readonly>
                                                @error('detalles.' . $key . '.materia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas_virtuales @error('detalles.' . $key . '.horas_virtuales') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_virtuales]" value="{{ old('detalles.' . $key . '.horas_virtuales', $materia['horas']) }}">
                                                @error('detalles.' . $key . '.horas_virtuales')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas_teams @error('detalles.' . $key . '.horas_teams') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_teams]" value="{{ old('detalles.' . $key . '.horas_teams', 0) }}">
                                                @error('detalles.' . $key . '.horas_teams')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas_presenciales @error('detalles.' . $key . '.horas_presenciales') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_presenciales]" value="{{ old('detalles.' . $key . '.horas_presenciales', 0) }}">
                                                @error('detalles.' . $key . '.horas_presenciales')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <textarea class="form-control text-center observaciones @error('detalles.' . $key . '.observaciones') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][observaciones]" cols="30" rows="1">{{ old('detalles.' . $key . '.observaciones') }}</textarea>
                                                @error('detalles.' . $key . '.observaciones')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        @php
                                            $key++;
                                        @endphp
                                    @endforeach
                                    @foreach ($materias_teams as $materia)
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-lg-4 mb-2 text-center">
                                                <input type="text" class="form-control text-center @error('detalles.' . $key . '.materia') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][materia]" value="{{ $materia['materia'] }}" readonly>
                                                @error('detalles.' . $key . '.materia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas horas_virtuales @error('detalles.' . $key . '.horas_virtuales') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_virtuales]" value="{{ old('detalles.' . $key . '.horas_virtuales', 0) }}">
                                                @error('detalles.' . $key . '.horas_virtuales')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas horas_teams @error('detalles.' . $key . '.horas_teams') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_teams]" value="{{ old('detalles.' . $key . '.horas_teams', $materia['horas']) }}">
                                                @error('detalles.' . $key . '.horas_teams')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas horas_presenciales @error('detalles.' . $key . '.horas_presenciales') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_presenciales]" value="{{ old('detalles.' . $key . '.horas_presenciales', 0) }}">
                                                @error('detalles.' . $key . '.horas_presenciales')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <textarea class="form-control text-center observaciones @error('detalles.' . $key . '.observaciones') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][observaciones]" cols="30" rows="1">{{ old('detalles.' . $key . '.observaciones') }}</textarea>
                                                @error('detalles.' . $key . '.observaciones')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        @php
                                            $key++;
                                        @endphp
                                    @endforeach
                                    @foreach ($materias_presenciales as $materia)
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-lg-4 mb-2">
                                                <input type="text" class="form-control text-center @error('detalles.' . $key . '.materia') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][materia]" value="{{ $materia['materia'] }}" readonly>
                                                @error('detalles.' . $key . '.materia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas horas_virtuales @error('detalles.' . $key . '.horas_virtuales') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_virtuales]" value="{{ old('detalles.' . $key . '.horas_virtuales', 0) }}">
                                                @error('detalles.' . $key . '.horas_virtuales')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas horas_teams @error('detalles.' . $key . '.horas_teams') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_teams]" value="{{ old('detalles.' . $key . '.horas_teams', 0) }}">
                                                @error('detalles.' . $key . '.horas_teams')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <input type="text" class="form-control text-center horas horas_presenciales @error('detalles.' . $key . '.horas_presenciales') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][horas_presenciales]" value="{{ old('detalles.' . $key . '.horas_presenciales', $materia['horas']) }}">
                                                @error('detalles.' . $key . '.horas_presenciales')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <textarea class="form-control text-center observaciones @error('detalles.' . $key . '.observaciones') is-invalid @enderror" id="materia-{{ $key }}" name="detalles[{{ $key }}][observaciones]" cols="30" rows="1">{{ old('detalles.' . $key . '.observaciones') }}</textarea>
                                                @error('detalles.' . $key . '.observaciones')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        @php
                                            $key++;
                                        @endphp
                                    @endforeach
                                    <div class="row d-flex justify-content-center align-items-center">
                                        <div class="col-lg-2 mb-2 text-end">
                                            <label class="form-label" for="total_horas">Total de Horas</label>
                                        </div>
                                        <div class="col-lg-2 mb-2">
                                            <input type="text" class="form-control text-center @error('total_horas_virtuales') is-invalid @enderror" id="total_horas_virtuales" name="total_horas_virtuales" value="{{ old('total_horas_virtual', $materias_virtuales->sum('horas')) }}" readonly>
                                            @error('total_horas_virtuales')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-2 mb-2">
                                            <input type="text" class="form-control text-center @error('total_horas_teams') is-invalid @enderror" id="total_horas_teams" name="total_horas_teams" value="{{ old('total_horas_virtual', $materias_teams->sum('horas')) }}" readonly>
                                            @error('total_horas_teams')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-2 mb-2">
                                            <input type="text" class="form-control text-center @error('total_horas_presenciales') is-invalid @enderror" id="total_horas_presenciales" name="total_horas_presenciales" value="{{ old('total_horas_virtual', $materias_presenciales->sum('horas')) }}" readonly>
                                            @error('total_horas_presenciales')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-center align-items-center">
                                        <div class="col-lg-2 mb-2 text-end">
                                            <label class="form-label" for="monto_total">Monto Total</label>
                                        </div>
                                        @php
                                            $monto_total = 0;
                                            $monto_total += $materias_virtuales->sum('horas') * $monto_virtual;
                                            $monto_total += $materias_teams->sum('horas') * $monto_teams;
                                            $monto_total += $materias_presenciales->sum('horas') * $monto_presencial;
                                        @endphp
                                        <div class="col-lg-6 mb-2 text-center">
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center @error('monto_total') is-invalid @enderror" id="monto_total" name="monto_total" value="{{ old('monto_total', number_format($monto_total, 0, ',', '.')) }}" readonly>
                                                @error('monto_total')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</a>
                            <a type="button" class="btn btn-success" id="save-btn">Generar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('docentes.scripts.show-horas-scripts')
    @endsection
{{-- @endcan --}}
