@can('ver_informes_academicos_alumnos_pantalla')
    <!DOCTYPE html>
    <html lang="sp">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>PDF</title>
        <!-- Bootstrap Css -->
        <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <style>
            @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path('fonts/OpenSans.ttf') }}) format("truetype");
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
            }
            /** Define now the real margins of every page in the PDF **/
            body {
                margin: 1.3cm;
                font-family: 'Open Sans', sans-serif;
            }

            .page-break {
                page-break-after: always;
            }
            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: auto;
                display: flex;
                align-items: flex-end;
                border-bottom: 1px solid black;
                text-align: center;
                margin-top: -50px;
            }
            #logo {
                width: auto;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                margin-top: 20px;
                margin-bottom: 5px;
            }
            #datos {
                display: inline-block;
                vertical-align: top;
                width: auto;
                height: auto;
                margin-top: 32.5px;
                margin-bottom: 5px;
                text-align: left!important;
                font-weight: bold;
            }

            /** Define the footer rules **/
            footer {
                position: fixed;
                bottom: 0cm;
                left: 0cm;
                right: 0cm;
                height: 0cm;
                display: flex;
                align-items: flex-end;
                font-size: 10px;
                width: auto;
                border-top: 1px solid black;
                margin-bottom: -15px;
            }
            .pagenum::after {
                content: counter(page);
            }
            #footer-text {
                width: 80%;
                display: inline-block;
                vertical-align: top;
                margin-top: 5px;
            }
            #pagenum {
                width: 19.5%;
                display: inline-block;
                vertical-align: top;
                text-align: right;
                margin-top: 5px;
            }
            #estudiante {
                font-size: 12px;
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            .bordes {
                border: 1px solid black;
            }
            .izquierda {
                width: 65%;
            }
            .derecha {
                width: 15%;
                text-align: right
            }
            .derecha-2 {
                width: 10%;
                text-align: center;
            }
            #primer_parrafo {
                width: 100%;
                text-align: justify;
                font-size: 12px;
            }
            .semestres {
                width: 100%;
                border: 1px solid black;
                margin-bottom: 15px;
            }
            .semestres thead{
                text-align: center;
                background-color: #9BC2E6;
                font-weight: bold;
            }
            .titulos {
                text-align: center;
                font-weight: bold;
            }
            .semestres td{
                font-size: 10px;
                border: 1px solid black;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <header>
            <div id="encabezado">
                <div id="logo">
                    <img src="{{asset('storage/logos/siu-dark-logo.png')}}" alt="logo-siu">
                </div>
                <div id="datos">
                    <div style="font-size: 12px">PROGRAM OF STUDY</div>
                    <div style="font-size: 12px">BACHELORT OF ARTS</div>
                    <div style="font-size: 12px">{{$carrera->nombre_real}}</div>
                </div>
            </div>
        </header>
        <footer>
            <div id="footer-text">
                <span><b>Generate on </b>{{Str::lower($fecha_hoy->format('l'))}} {{$fecha_hoy->format('d/m/Y')}} <b>at</b> {{$fecha_hoy->format('H:i:s')}} <b></span>
            </div>
            <div class="pagenum" id="pagenum"><span>Page </span></div>
        </footer>
        <main>
            <div class="col-12 mb-1">
                <table id="estudiante">
                    <tbody>
                        <tr>
                            <td class="izquierda"><b>STUDENT NAME:</b> {{Str::title($alumno->apellido_nombre)}}</td>
                            <td class="derecha"><b>STUDENT ID:</b></td>
                            <td class="bordes derecha-2">{{number_format($alumno->numero_documento, 0, ',', '.')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @foreach ($semestres as $key => $semestre)
                <table class="semestres">
                    <thead>
                        <tr>
                            <td style="font-size: 14px;" colspan="6">Term {{$semestres_romanos[$key]['numero']}}</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="titulos">
                            <td style="width: 15%">Course Number</td>
                            <td style="width: 38%">Course Title</td>
                            <td style="width: 12%">Credit Hours</td>
                            <td style="width: 15%">Term / Year Taken</td>
                            <td style="width: 10%">Grade</td>
                            <td style="width: 10%">Transfer</td>
                        </tr>
                        @foreach ($alumno_notas as $nota)
                            @if ($nota->semestre_materia == $semestre['numero'])
                                <tr>
                                    <td class="text-center">{{$nota->materia->codigo}}</td>
                                    <td>{{$nota->materia->nombre_real}}</td>
                                    <td class="text-center">{{$nota->materia->carga_horaria}}</td>
                                    <td class="text-center">{{$nota->fecha}}</td>
                                    <td class="text-center">{{$nota->calificacion}}</td>
                                    <td class="text-center">--------------</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </main>
    </body>
    </html>
@endcan
