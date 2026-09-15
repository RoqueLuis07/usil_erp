@can('generar_certificados_maestrias_ubs')
    @extends('layouts.pdf.notas')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
                font-size: 20px;
            }
            #certificado {
                margin-bottom: -1px;
            }
            #primer_parrafo {
                width: 100%;
                text-align: justify;
                font-size: 12px;
            }
            .semestres {
                width: 100%;
                border: 1px solid black;
                margin-top: -12px;
                margin-bottom: 15px;
            }
            .semestres thead{
                text-align: center;
                background-color: #BFBFBF;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            .nombre_semestre{
                border-bottom: 1px solid black;
            }
            .semestres td{
                font-size: 10px;
                border: 1px solid black;
                padding: 5px;
            }
            .semestres tfoot{
                font-size: 10px;
                padding: 10px;
            }
            #totales{
                font-size: 14px;
                font-weight: bold;
                margin-top: -12px;
            }
            .titulo_totales{
                padding-right: 2em;
            }
            #segundo_parrafo {
                width: 100%;
                text-align: justify;
                font-size: 12px;
            }
            #firmas {
                position: fixed;
                width: 90%;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                font-size: 14px;
                font-weight: bold;
                margin-top: 1.5cm;
            }
            #suscribe {
                width: 200px;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 50px;
            }
            #rector {
                display: inline-block;
                vertical-align: top;
                width: 200px;
                height: auto;
                text-align: center;
                border-top: 1px solid black;
                margin-left: 50px;
                position: relative;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="certificado"><b>CERTIFICADO DE ESTUDIOS</b></p>
        </div>
        <div id="primer_parrafo">
            <p>La que suscribe, <b>{{$nombre_suscribe}}</b>, Secretaria General de la {{$empresa->nombre_fantasia}} - Paraguay,
                <b>CERTIFICA</b> que según constancia obrante en Secretaria, de esta casa de estudios, {{$alumno->pronombre}}
                <b>{{$alumno->apellido_nombre}}</b> con documento de identidad número
                {{number_format($alumno->numero_documento, 0, ',', '.')}} de nacionalidad {{$alumno->nacionalidad}}; ha obtenido
                las siguientes calificaciones durante el proceso académico vigente de la <b>{{$curso->nombre_real}}</b>;
                según constancias obrantes en los Libros de Actas respectivos y
                en la malla curricular, que a continuación se expresan:
            </p>
        </div>
        @foreach ($semestres as $semestre)
            <table class="semestres">
                <thead>
                    <tr class="nombre_semestre">
                        <th colspan="7" style="text-align: left; font-size: 14px;">Semestre {{$semestre['numero']}}</th>
                    </tr>
                    <tr>
                        <th style="width: 25%">Asignatura</th>
                        <th style="width: 15%">Número de Acta</th>
                        <th style="width: 12%">Fecha</th>
                        <th style="width: 10%">Calificación N° y Letras</th>
                        <th style="width: 18%">Periodo de Examen</th>
                        <th style="width: 15%">Carga Horaria Asig.</th>
                        <th style="width: 10%">Modalidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumno_notas as $nota)
                        @if ($nota->semestre_modulo == $semestre['numero'])
                            <tr>
                                <td>{{$nota->modulo->nombre_real}}</td>
                                <td class="text-center">{{$nota->numero_acta}}</td>
                                <td class="text-center">{{\Carbon\Carbon::parse($nota->fecha)->format('d/m/Y')}}</td>
                                <td class="text-center">{{$nota->calificacion}} ({{$nota->calificacion_letras}})</td>
                                <td class="text-center">{{$nota->evaluacion}}</td>
                                <td class="text-center">{{$nota->modulo->carga_horaria}}</td>
                                <td class="text-center">{{$nota->curso->modalidad->nombre}}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Promedio Académico</td>
                        <td colspan="6" style="text-align: center;"><b>{{number_format($semestre['promedio'], 2, ',', '.')}}</b></td>
                    </tr>
                    <tr>
                        <td>Carga Horaria Total</td>
                        <td class="text-center" colspan="6"><b>{{$semestre['carga_horaria']}}</b></td>
                    </tr>
                </tfoot>
            </table>
        @endforeach
        <table id="totales">
            <tbody>
                <tr>
                    <td class="titulo_totales">PROMEDIO GENERAL:</td>
                    <td>{{number_format($promedio_general, 2, ',', '.')}} ({{$promedio_general_letras}})</td>
                </tr>
                <tr>
                    <td class="titulo_totales">CARGA HORARIA TOTAL:</td>
                    <td>{{number_format($carga_horaria_general)}} ({{$carga_horaria_general_letras}}) horas</td>
                </tr>
                <tr>
                    <td class="titulo_totales">DURACIÓN DE LA CARRERA</td>
                    <td>{{$curso->duracion}} años</td>
                </tr>
            </tbody>
        </table>
        <br>
        <div id="segundo_parrafo">
                <p>
                    <b>OBSERVACIÓN</b>
                    <br><br>
                    La escala de calificaciones y las referencias se dan como sigue:
                    <br><br>
                    <b>1:</b> Insuficiente <b>2:</b> Aceptable <b>3:</b> Bueno <b>4:</b> Muy Bueno <b>5:</b> Excelente
                    <br><br>
                    En la ciudad de Asunción, capital de la República del Paraguay, a los {{$fecha_hoy->day}} días del mes
                    de {{$fecha_hoy->locale('es')->translatedFormat('F')}} del año {{$fecha_hoy->year}}, se hace
                    constar que este
                    @if ($curso->terminado)
                        <b>CERTIFICADO HABILITA</b> a <b>{{$alumno->nombre_apellido}}</b> para obtener el título de
                        {{Str::title($curso->titulo_alumno)}} de Conformidad con la Ley {{$curso->numero_ley}}
                        y el Acta N° {{$curso->numero_acta}} - RESOLUCIÓN CONES {{$curso->numero_resolucion_cones}}.-
                    @else
                        <b>CERTIFICADO NO HABILITA</b> a <b>{{$alumno->nombre_apellido}}</b> para obtener el título de
                        {{Str::title($curso->titulo_alumno)}} de Conformidad con la Ley {{$curso->numero_ley}}
                        - RESOLUCIÓN CONES {{$curso->numero_resolucion_cones}}.-
                    @endif
                    <br>
                    @if ($curso->terminado && $tesis)
                        {{-- cambiar este sector cuando haya modulo de tesis --}}
                            Habiendo obtenido la calificación <b>{{number_format($tesis->calificacion, 2, ',', '.')}} ({{$tesis->calificacion_letras}})</b> en la defensa, cuyo título de tesis denominado:
                            <b>"{{Str::title($tesis->titulo)}}"</b> en fecha {{$tesis->fecha_defensa->format('d')}} de {{$tesis->fecha_defensa->locale('es')->translatedFormat('F')}} del {{$tesis->fecha_defensa->format('Y')}}.
                        {{-- cambiar hasta aca --}}
                    @endif
                </p>
        </div>
        <div id="firmas">
            <div id="suscribe">
                <div>
                    {{$nombre_suscribe}}
                    <br>
                    Secretaria General
                </div>
            </div>
            <div id="rector">
                <div>
                    {{$nombre_rector}}
                    <br>
                    Rector
                </div>
            </div>
        </div>
    @endsection
@endcan
