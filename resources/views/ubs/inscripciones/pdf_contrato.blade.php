@can('imprimir_contratos_inscripciones_ubs')
    @extends('layouts.pdf.contratos')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
                font-size: 20px;
            }
            #contrato {
                margin-bottom: -1px;
            }
            .pagina {
                position: fixed;
                width: 100%;
                height: auto;
                display: flex;
                font-size: 12px;
                margin-top: 5px;
            }
            .titulos_numerados {
                width: 90%;
                font-weight: bold;
            }
            .parrafo {
                width: 90%;
            }
            #estudiante_parrafo {
                width: 100%;
                text-align: center;
            }
            .tabla_estudiante {
                margin: auto;
                width: 80%;
                text-align: left;
                font-size: 12px;
            }
            .izquierda {
                width: 40%;
            }
            .derecha {
                width: 60%;
            }
            #egreso {
                text-align: left!important;
                width: 80%!important;
                margin-left: 1.88cm;
            }
            .titulos_estudiante {
                font-weight: bold;
                text-align: left!important;
                width: 80%!important;
                margin-left: 1.88cm;
                margin-bottom: -1px;
            }

            .checkbox {
                width: 30px;
                height: 30px;
                margin-right: 10px;
                border: 2px solid limegreen;
                display: inline-block;
                position: relative;
                cursor:pointer;
            }
            input[type="checkbox"] {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                color: white;
                width: 30px;
                height: 30px;
                cursor: pointer;
            }
            input[type="checkbox"]:checked::before,
            input[type="checkbox"]:checked::after {
                content: '';
                position: absolute;
                background-color: black;
                width: 4px;
                height: 26px;
            }
            input[type="checkbox"]:checked::before {
                transform: rotate(45deg);
                top: 2px;
                left: 13px;
            }
            input[type="checkbox"]:checked::after {
                transform: rotate(-45deg);
                top: 2px;
                left: 13px;
            }

            .tabla_aranceles {
                margin: auto;
                width: 80%;
                text-align: left;
                font-size: 12px;
                border: 1px solid black;
            }
            .aranceles_checkbox {
                width: 10%;
            }
            .aranceles_izquierda {
                width: 45%;
                border-right: 1px solid black;
                border-top: 1px solid black;
            }
            .aranceles_derecha {
                width: 45%;
                border-top: 1px solid black;
            }

            .lista {
                width: 85%;
                text-align: justify;
            }

            .letras_chicas {
                width: 90%;
                font-size: 9px;
            }

            .nota {
                width: 90%;
                font-size: 10px;
                font-weight: bold;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="contrato"><b>CONTRATO</b></p>
        </div>
        <div class="pagina">
            <p class="parrafo">{{$estimado}} estudiante: <b>{{$nombre_alumno}}</b></p>
            <p class="parrafo">Por medio del presente documento, le entregamos la información sobre la normativa de la Universidad.</p>
            <p class="parrafo">
                El CONTRATO deberá ser <b>firmado por el interesado al momento de su inscripción</b> en la Sección
                de Admisión de la Universidad, previa verificación que, los datos registrados estén correctos. Esto
                habilitará la opción de pago del importe correspondiente a su matrícula y/o cuota.
            </p>
            <p class="parrafo">Le recordamos que, de no proceder con lo indicado, su matrícula quedará pendiente.</p>
            <p class="titulos_numerados">1. &nbsp;&nbsp;&nbsp;&nbsp; DATOS DEL CONTRATO DE INGRESO</p>
            <div id="estudiante_parrafo">
                <table class="tabla_estudiante">
                    <tbody>
                        <tr>
                            <td class="izquierda">Programa:</td>
                            <td class="derecha">{{$inscripcion->curso->nombre_real}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Créditos del programa:</td>
                            <td class="derecha">{{number_format($inscripcion->curso->cantidad_creditos, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Duración del programa:</td>
                            <td class="derecha">{{number_format($inscripcion->curso->cantidad_horas, 0, ',', '.')}} horas - {{$inscripcion->curso->duracion}} @if ($inscripcion->curso->duracion != 1) años @else año @endif</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Formación Académica del postulante:</td>
                            <td class="derecha">{{$inscripcion->alumno->formacion->nombre}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Institución de procedencia:</td>
                            <td class="derecha">{{$inscripcion->alumno->institucionEducativa->nombre}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Periodo de ingreso:</td>
                            <td class="derecha">{{$periodo_ingreso}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Periodo de egreso (*):</td>
                            <td class="derecha">{{$periodo_egreso}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Cédula de Identidad Civil:</td>
                            <td class="derecha">{{$inscripcion->alumno->numero_documento}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Correo Personal:</td>
                            <td class="derecha">{{$inscripcion->alumno->email_personal}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Teléfono / Celular:</td>
                            <td class="derecha">@if ($inscripcion->alumno->telefono) {{$inscripcion->alumno->telefono}} / @endif {{$inscripcion->alumno->celular}}</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p id="egreso">
                    (*) El periodo de egreso es un cálculo aproximado y está sujeto al rendimiento académico
                    del estudiante durante los semestres del desarrollo de la carrera.
                </p>
                <p class="titulos_estudiante">Domicilio</p>
                <table class="tabla_estudiante">
                    <tbody>
                        <tr>
                            <td class="izquierda">Dirección</td>
                            <td class="derecha">{{$inscripcion->alumno->direccion}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Barrio:</td>
                            <td class="derecha">{{$inscripcion->alumno->barrio->nombre}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Ciudad:</td>
                            <td class="derecha">{{$inscripcion->alumno->ciudad->nombre}}</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p class="titulos_estudiante">Facturación</p>
                <table class="tabla_estudiante">
                    @php
                        foreach ($inscripcion->alumno->alumnoClientes as $cliente) {
                            if ($cliente->es_principal) {
                                $cliente_razon_social = $cliente->cliente->nombre;
                                $cliente_ruc = $cliente->cliente->numero_documento;
                            }
                        }
                    @endphp
                    <tbody>
                        <tr>
                            <td class="izquierda">Razón Social:</td>
                            <td class="derecha">{{$cliente_razon_social}}</td>
                        </tr>
                        <tr>
                            <td class="izquierda">R.U.C.:</td>
                            <td class="derecha">{{$cliente_ruc}}</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p class="titulos_estudiante">Datos Laborales (si corresponde)</p>
                <table class="tabla_estudiante">
                    <tbody>
                        <tr>
                            <td class="izquierda">Empresa:</td>
                            <td class="derecha">
                                @if ($inscripcion->alumno->dato_laboral_id)
                                    {{$inscripcion->alumno->datoLaboral->empresa}}
                                @else
                                    ________________________________________________
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="izquierda">Teléfono:</td>
                            <td class="derecha">
                                @if ($inscripcion->alumno->dato_laboral_id)
                                    {{$inscripcion->alumno->datoLaboral->telfono}}
                                @else
                                    ________________________________________________
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="izquierda">Correo:</td>
                            <td class="derecha">
                                @if ($inscripcion->alumno->dato_laboral_id)
                                    {{$inscripcion->alumno->datoLaboral->email}}
                                @else
                                    ________________________________________________
                                @endif
                                </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p class="titulos_estudiante">Datos Familiares (si corresponde)</p>
                <table class="tabla_estudiante">
                    @php
                        if ($inscripcion->alumno->familiar_uno_id) {
                            $relacion = Str::title($inscripcion->alumno->familiarUno->relacion->nombre);
                        }
                    @endphp
                    <tbody>
                        <tr>
                            <td class="izquierda">{{$relacion}}</td>
                            <td class="derecha">
                                @if ($inscripcion->alumno->familiar_uno_id)
                                    {{$inscripcion->alumno->familiarUno->primer_nombre}} {{$inscripcion->alumno->familiarUno->primer_apellido}}
                                @else
                                    ________________________________________________
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="izquierda">Teléfono:</td>
                            <td class="derecha">
                                @if ($inscripcion->alumno->familiar_uno_id)
                                    {{$inscripcion->alumno->familiarUno->celular}}
                                @else
                                    ________________________________________________
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="izquierda">Correo:</td>
                            <td class="derecha">
                                @if ($inscripcion->alumno->familiar_uno_id)
                                    {{$inscripcion->alumno->familiarUno->email}}
                                @else
                                    ________________________________________________
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table class="tabla_estudiante">
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="checkbox" id="checkbox"></td>
                            <td>
                                Por este medio autorizo expresamente a que la/s persona/s detalladas más abajo,
                                tengan acceso a toda la información académica y administrativa a lo largo de la
                                carrera universitaria: (marca con una X en caso de aceptación y completar los datos)
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p class="titulos_estudiante">Autorizado (si corresponde)</p>
                <table class="tabla_estudiante">
                    <tbody>
                        <tr>
                            <td class="izquierda">Madre/Padre/Familiar</td>
                            <td class="derecha">________________________________________________</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Teléfono:</td>
                            <td class="derecha">________________________________________________</td>
                        </tr>
                        <tr>
                            <td class="izquierda">Correo:</td>
                            <td class="derecha">________________________________________________</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="page-break"></div>
        <div class="pagina">
            <p class="titulos_numerados">
                2. &nbsp;&nbsp;&nbsp;&nbsp; ACEPTACIÓN DE TÉRMINOS Y CONDICIONES DE MATRÍCULA,
                REGLAMENTO DE ESTUDIOS Y CONDICIONES ECONÓMICAS
            </p>
            <p class="parrafo">
                Los documentos que usted ha confirmado conocer son los vigentes y podrá descargarlos
                desde <u>http://www.usil.edu.py</u> como (Menú INSTITUCIONAL, opción Institucional Reglamentos
                y Manual del Estudiante).
            </p>
            <p class="parrafo">
                En estos documentos se informan, entre otras cosas, lo siguiente:
            </p>
            <p class="titulos_estudiante">2.1 &nbsp; Información sobre los requisitos para:</p>
            <br>
            <table class="tabla_estudiante">
                <tbody>
                    <tr>
                        <td><input type="checkbox" class="checkbox" id="checkbox"></td>
                        <td>
                            <span><b>a) Obtener el Título de Licenciado de la USIL, el estudiante deberá:</b></span>
                            <ul>
                                <li>1 (una) fotocopia autenticada de la cédula de identidad.</li>
                                <li>Presentar 1 (una) fotocopia simple del certificado de estudios de Bachiller.</li>
                                <li>Presentar 1 (una) fotocopia simple del título de Bachiller (ambas caras).</li>
                                <li>Título de Bachiller traducido al inglés por un traductor público.</li>
                                <li>Certificado de estudios del bachiller traducido al inglés por un traductor público.</li>
                                <li>1 (una) foto tipo carnet 3x3 cm.</li>
                                <li>Tener aprobadas todas las materias de la malla curricular vigente.</li>
                                <li>Cumplir con sus obligaciones administrativas ante la Universidad.</li>
                                <li>Presentar y defender el Trabajo Final de Grado.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="checkbox" id="checkbox"></td>
                        <td>
                            <span><b>b) Para obtener el Título de Capacitado en o Especialista en: Los documentales requeridos son:</b></span>
                            <ul>
                                <li>2 (dos) copias de Cédula de Identidad auntenticadas por escribanía.</li>
                                <li>1 (una) copia del Título de Grado, autenticada por escribanía.</li>
                                <li>1 (una) copia de Certificado de Estudios auntenticada por escribanía.</li>
                                <li>2 (dos) fotos tipo carnet, actualizadas y a colores.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="checkbox" id="checkbox" @if ($inscripcion->curso->es_maestria == true) checked @endif></td>
                        <td>
                            <span><b>c) Para obtener el Título de Mágister de la USIL: el estudiante debe además de los puntos en común con los programas de arriba:</b></span>
                            <ul>
                                <li>Elaboración y Defensa de la Tesis. Los documentales requeridos son:</li>
                                <li>2 (dos) copias de Cédula de Identidad auntenticadas por escribanía.</li>
                                <li>1 (una) copia del Título de Grado, autenticada por escribanía.</li>
                                <li>1 (una) copia de Certificado de Estudios auntenticada por escribanía.</li>
                                <li>2 (dos) fotos tipo carnet, actualizadas y a colores.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="checkbox" id="checkbox"></td>
                        <td>
                            <span><b>En caso de ser alumno extranjero para POSGRADOS, los documentales requeridos son:</b></span>
                            <ul>
                                <li>2 (dos) copias del Documento de Identidad, DNI, Cartera de Identidad conforme al país de origen autenticado por escribanía paraguaya.</li>
                                <li>1 (una) copia del Título de Grado y un ejemplar original de Certificado de Estudios que deben reunir los siguientes requisitos:
                                    Los títulos de GRADO o POSGRADOS deben estar revalidados y/o HOMOLOGADOS en Paraguay.
                                    Legalización del Ministerio de Relaciones Exteriores del Paraguay. Visación y legalización, por el Ministerio
                                    de Educación y Ciencias del Paraguay, <b>o en su defecto, deben cumplir con el requisito del APOSTILLADO.</b>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <span><b>En todo los casos, para obtener los títulos deben:</b></span>
                            <ul>
                                <li>Tener aprobada toda la malla curricular vigente.</li>
                                <li>Cumplri con sus obligaciones administrativas ante la Universidad.</li>
                                <li>Si se necesitare documentación adicional requerida se le hará saber al alumno oportunamente.</li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <p class="titulos_estudiante">2.2 &nbsp; Promoción de empleo:
                <span style="font-weight:normal!important">
                    La Universidad San Ignacio de Loyola podrá brindar apoyo a sus estudiantes en los procesos de reclutamiento de las
                    empresas a través de su página Web; sin embargo, no asegura la obtención de plazas vacantes.
                </span>
            </p>
        </div>
        <div class="page-break"></div>
        <div class="pagina">
            <p class="titulos_numerados">3. &nbsp;&nbsp;&nbsp;&nbsp; INFORMACIÓN SOBRE EL INICIO DEL PLAN DE ESTUDIOS</p>
            <p class="titulos_estudiante">3.1 &nbsp;
                <span style="font-weight:normal!important">
                    Para el desarrollo de las carreras, posgrados en cada periodo, se facilitará al alumno
                    el calendario académico conforme al programa seleccionado.
                </span>
            </p>
            <p class="titulos_estudiante">3.2 &nbsp;
                <span style="font-weight:normal!important">
                    La Universidad dicta las clases presenciales, semi presenciales o virtuales dependiendo
                    de la modalidad del programa seleccionado, en el local ubicado en Venezuela casi Artigas
                    de la ciudad de Asunción, Paraguay.
                </span>
            </p>
            <p class="titulos_estudiante">3.3 &nbsp;
                <span style="font-weight:normal!important">
                    En el caso de existir convalidaciones deberá presentar:
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp; a) &nbsp; 1 (un) original o copia autenticada del programa foliado de las asignaturas a convalidar.
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp; b) &nbsp; 1 (un) Certificado de estudios original de la Universidad de origen.
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp; c) &nbsp; Abonar el canon correspondiente a la convalidación, salvo decisión expresa del Consejo Directivo de exonerar la misma.
                </span>
            </p>
            <p class="titulos_numerados">4. &nbsp;&nbsp;&nbsp;&nbsp; INFORMACIÓN SOBRE ARANCELES</p>
            <table class="tabla_aranceles">
                <tbody>
                    <tr>
                        <td class="aranceles_checkbox"><input type="checkbox" class="checkbox" id="checkbox" ></td>
                        <td class="aranceles_izquierda">PARA LICENCIATURAS</td>
                        <td class="aranceles_checkbox"><input type="checkbox" class="checkbox" id="checkbox" @if ($inscripcion->curso->es_maestria == true) checked @endif></td>
                        <td class="aranceles_derecha">PARA POSGRADOS</td>
                    </tr>
                    <tr>
                        <td class="aranceles_izquierda" colspan="2">Monto de matrícula por semestre: </td>
                        <td class="aranceles_derecha" colspan="2">Precio General/Valor Contado: Gs. {{number_format($precios->precio_contado, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td class="aranceles_izquierda" colspan="2">Número de matrículas: </td>
                        <td class="aranceles_derecha" colspan="2">Financiación: @if ($inscripcion->tipo_pago == 'CO') N/A @else {{number_format($precios->cantidad_cuotas, 0, ',', '.')}} pagos de Gs. {{number_format($precios->precio_cuota, 0, ',', '.')}} @endif</td>
                    </tr>
                    <tr>
                        <td class="aranceles_izquierda" colspan="2">Monto de cuota: </td>
                        <td class="aranceles_derecha" colspan="2">Importe Def. de Tesis + Titulación: Gs. {{number_format(($precios->precio_defensa + $precios->precio_titulo), 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td class="aranceles_izquierda" colspan="2">Número de cuotas por semestre: </td>
                        <td class="aranceles_derecha" colspan="2">Beneficio (si aplica detallar): </td>
                    </tr>
                    <tr>
                        <td class="aranceles_izquierda" colspan="2">Beneficio (si aplica): </td>
                        <td class="aranceles_derecha" colspan="2">Monto a pagar con beneficio (si aplica): </td>
                    </tr>
                    <tr>
                        <td class="aranceles_izquierda" colspan="2">Monto a pagar con beneficio (si aplica): </td>
                        <td class="aranceles_derecha" colspan="2">Otros (especificar): </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="tabla_estudiante">
                <tbody>
                    <tr>
                        <td>Forma de Pago:</td>
                        <td>Contado</td>
                        <td><input type="checkbox" class="checkbox" id="checkbox" @if ($inscripcion->tipo_pago == 'CO') checked @endif></td>
                        <td>Mensual</td>
                        <td><input type="checkbox" class="checkbox" id="checkbox" @if ($inscripcion->tipo_pago == 'CR') checked @endif></td>
                        <td>Tarjeta de Débito/Crédito</td>
                        <td><input type="checkbox" class="checkbox" id="checkbox"></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <p class="titulos_estudiante"><u>Importante de acuerdo al programa seleccionado:</u></p>
            <p class="parrafo">
                <ul class="lista">
                    <li>La primera matrícula al momento de la inscripción es la única exonerada.</li>
                    <li>El monto de la cuota puede ser objeto de variación, conforme a la Política Institucional
                        y el Reglamento de Aranceles vigente. Los pagos de cuotas son por mes adelantado del 01
                        al 10 de cada mes, la falta del cumplimiento administrativo al día 11 ocasionará la pérdida
                        de todos sus derechos académicos, hasta tanto ponerse al día nuevamente. La falta de pago
                        de las cuotas en el plazo indicado ocasionará la mora automática del alumno y deberá abonar
                        una tasa de interés moratoria del 3% mensual.
                    </li>
                    <li>Los beneficios están sustentados por los convenios firmados con empresas o entidades seleccionadas.
                        El descuento es sujeto de renovación siempre y cuando los convenios se encuentren vigentes y el
                        alumno beneficiado con el mismo no se encuentre en mora.
                    </li>
                    <li><b>En caso de haber recibido algún tipo de beca, la consideración de ésta, estará
                        sujeta a la siguiente condición:</b> No encontrarse en mora con la cuota estipulada.</li>
                    <li>Autorizo a USIL en forma expresa e irrevocable, otorgando suficiente mandato en los términos
                        del Art. 917 inc. A) del Código Civil para que por propia cuenta o a través de empresas
                        especializadas de nuestro medio, puedan consultar informaciones de los registros públicos o
                        privados en esta plaza comercial o en otra, referente a mi situación patrimonial, solvencia
                        económica, o el cumplimiento de mis obligaciones comerciales, como también para que en caso
                        de incumplimiento o retraso de mis obligaciones de más de 90 días la USIL podría incluir mi
                        nombre, en el Registro de Morosos de INFORMCONF u otra empresa dedicada a este tipo de actividad.
                    </li>
                    <li>La USIL se reserva el derecho de actuar conforme a las políticas institucionales administrativas
                        y académicas, respecto al incumplimiento de las obligaciones de parte de los alumnos.
                    </li>
                    <li>La USIL revisará el anexo correspondiente al Reglamento de Aranceles, semestralmente, en el
                        caso de existir modificaciones publicará en la página web de USIL el anexo actualizado a los
                        efectos de que la información sobre los aranceles vigentes sea notificada a los alumnos. Es
                        responsabilidad de los alumnos, estar al tanto de la información, no pudiendo alegar en ningún caso
                        desconocimiento de la misma.
                    </li>
                </ul>
            </p>
        </div>
        <div class="page-break"></div>
        <div class="pagina">
            <p class="titulos_numerados">5. &nbsp;&nbsp;&nbsp;&nbsp; INFORMACIÓN ECONÓMICA</p>
            <p style="margin-left:0.78cm; margin-top:-10px;"><u>Consideraciones:</u></p>
            <p class="titulos_estudiante" style="margin-top:-10px">a. &nbsp;<span style="font-weight:normal!important"><u>Retiros de alumnos Nuevos o Definitivos:</u></span></p>
            <p style="margin-top:-10px;">
                <ul style="margin-left:2cm;width:75%">
                    <li>Una vez realizada la matrícula académica, lo abonado por dicho concepto no es reembolsable.</li>
                    <li>El estudiante que haya pagado la totalidad del periodo académico, y se retire, perderá el derecho
                        a la devoluación de pago que hubiere efectuado.
                    </li>
                </ul>
            </p>
            <p class="titulos_estudiante" style="margin-top:-10px">b. &nbsp;<span style="font-weight:normal!important"><u>Reingreso:</u></span></p>
            <p style="margin-top:-10px;">
                <ul style="margin-left:2cm;width:75%">
                    <li>En caso de que el estudiante retorne sus estudios, deberá completar las cuotas que dejó de pagar
                        por la prestación efectiva del servicio educativo con los respectivos gastos y moras a la fecha
                        de pago, siempre y cuando no haya informado por escrito a la Institución; así como el importe de
                        reingreso y el derecho de reincorporación según la tarifa vigente a la fecha de su reingreso.
                    </li>
                    <li>En caso de no llevar a cabo el proceso de desmatriculación, los compromisos correspondientes seguirán
                        considerándose como morosos hasta completar dicho proceso.
                    </li>
                </ul>
            </p>
            <p class="titulos_estudiante" style="margin-top:-10px">c. &nbsp;<span style="font-weight:normal!important">En caso de no llevar a cabo el proceso de desmatriculación, los compromisos correspondientes seguirán
                considerándose como morosos hasta completar dicho proceso.</span>
            </p>
            <p class="titulos_estudiante" style="margin-top: 5px;">d. &nbsp;<span style="font-weight:normal!important"><u>Forma de pago:</u></span></p>
            <p style="margin-top:5px; margin-left: 2.38cm;">Pago en cuotas:</p>
            <p style="margin-top:-30px;">
                <ul style="margin-left:2cm;width:75%">
                    <li>La Universidad San Ignacion de Loyola establece sus precios por periodo académico (un semestre);
                        sin embargo, brinda la facilidad a sus estudiantes de realizar los pagos en cuotas mensuales de acuerdo
                        con la categoría de pago asignada.
                    </li>
                    <li>Los pagos se realizan en la fecha consignada en las factueas y atendiendo el concepto de las mismas.</li>
                    <li>Pago por periodo académico: Los estudiantes que así lo requieran pueden realizar el pago por todo el
                        periodo académico. Dicho pago considera un porcentaje de descuento sobre el costo total por la prestación
                        del servicio educativo por cada periodo académico, y de acuerdo a la aprobación correspondiente, aceptando
                        que queda a criterio de la Institución determinar dicho porcentaje.
                    </li>
                    <li>La inversión total del programa, especialización, capacitación, maestría y doctorados en general serán
                        pagaderos por adelantado. No se admiten reembolsos ni devoluciones. Sin embargo, la Universidad podrá
                        financiar la inversión total del programa, especialización, capacitación, maestría, doctorados y
                        diplomados en general en cuotas iguales y consecutivas sin reembolsos o devoluciones.
                    </li>
                    <li>La falta de pago de dos (2) cuotas de la financiación hará decaer el plazo de las cuotas tornándose
                        éstas automáticamente exigibles en su totalidad, sin beneficio alguno.
                    </li>
                    <li>La ausencia temportal o permanente de las clases, del programa, especialización, capacitación, maestría
                        y doctorados en general no será causal para incumplir con el pago total de la inversión de cada programa
                        seleccionado por el alumno; salvo el caso de realizado el proceso administrativo de desinscripción
                        correspondiente para cada programa. Al momento de retirarse, el alumno antes de finalizar el programa
                        seleccionado, debe estar al día con el pago de los servicios utilizados.
                    </li>
                </ul>
            </p>
            <p class="letras_chicas">
                <span>
                    * &nbsp; Responsabilidad: las partes dejan constancia que no será responsabilidad de la USIL los perjuicios
                    derivados de la pérdida, daños, sustracción de efectos personales, artículos, bienes de cualquier clase
                    naturaleza del estudiante, que se introduzca o se mantengan en la USIL.
                </span>
                <br>
                <span>
                    * &nbsp; Obligación: La firma del presente instrumento representa una obligación tanto para el alumno como
                    para la USIL, en ese sentido la USIL se obliga a prestar los servicios educacionales siempre y cuando se den
                    las condiciones establecidas en los Reglamentos y Políticas Institucionales; así como también un mínimo de
                    alumnos que permita la continuidad del curso académico que se está llevando a cabo; debiendo el alumno dar
                    cumplimiento a sus obligaciones administrativas y académicas.
                </span>
                <br>
                <span>
                    El alumno se obliga a abonar los aranceles establecidos en el presente instrumento y en el reglamento interno
                    de aranceles. Así también las obligaciones establecidas en el presente instrumento son exigibles desde la fecha
                    de la firma del contrato.
                </span>
                <br>
                <span>
                    * &nbsp; Domicilios, jurisdicción y competencia: El alumno constituye domicilio en la casa de la calle más arriba
                    señalada, para todos los efectos emergentes de este contrato, donde se tendrán por válida todas las notificaciones
                    que se cursaren por recibidas las facturas que allí se remitan. La USIL constituye domicion en Avda. Venezuela 2087
                    de la ciudad de Asunción, tanto la USIL como el alumno se somete a la jurisdicción de los Tribunales Ordinarios
                    de Fuero Civil y Comercial de la ciudad de Asunción, con exclusión de todo fuero y jurisdicción.
                </span>
            </p>
            <p class="nota">Nota.- &nbsp;&nbsp;&nbsp;&nbsp;
                <span style="font-weight:normal!important; font-style:italic;">
                    El estudiante confirma tener una copia de este documento. Asimismo, declara haber leído en la página web de
                    la USIL las políticas contenidas en el catálogo de la Universidad.
                </span>
            </p>
        </div>
    @endsection
@endcan
