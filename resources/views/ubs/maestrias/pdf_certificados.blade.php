@can('generar_certificados_maestrias_ubs')
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
            @page {
                margin: 0;
            }
            /** Define now the real margins of every page in the PDF **/
            body {
                margin: 0;
                padding: 0;
                height: 100vh;
                height: 100vw;
                overflow: hidden;
                font-family: 'Open Sans', sans-serif;
            }
            .page-break {
                page-break-after: always;
            }
            /** Define the header rules **/
            .p1-image {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('{{asset('storage/cursos/certificados/p1.png')}}') no-repeat center center;
                background-size: cover;
                z-index: -10000;
            }
            .p1-content {
                position: relative;
                z-index: 1;
                padding: 20px;
                color: #000;
            }
            .nombre_alumno {
                width: 100%;
                text-align: center;
                margin-top: 34%;
                font-style: bold;
            }
            .curso {
                width: 100%;
                text-align: center;
                margin-top: 1em;
            }
            .firmas {
                width: 100%;
                height: 4.22cm;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                margin-top: 3cm;
            }
            .secretaria {
                width: 200px;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 50px;
            }
            .rector {
                display: inline-block;
                vertical-align: top;
                width: 200px;
                height: auto;
                text-align: center;
                border-top: 1px solid black;
                margin-left: 50px;
                position: relative;
            }
            .p2-image {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('{{asset('storage/cursos/certificados/p2.png')}}') no-repeat center center;
                background-size: cover;
                z-index: -10000;
            }
            .p2-content {
                position: relative;
                z-index: 1;
                padding: 20px;
                color: #04218C;
                height: 94.96%;
                /* font-style: bold; */
            }
            .p2-text {
                width: 8.5cm;
                text-align: justify;
                margin-top: 43%;
                margin-left: 65%;
            }
        </style>
    </head>
    <body>
        @foreach ($datos as $dato)
            <div class="p1-image"></div>
            <div class="p1-content">
                <div class="nombre_alumno">
                    <h1>{{$dato['nombre_alumno']}}</h1>
                </div>
                <div class="curso">
                    <p>por haber participado en el {{$curso->nombre_real}}, organizado por la
                        <br>
                        Universidad San Ignacio de Loyola, en el período del {{$curso->inicio}} al {{$curso->fin}},
                        <br>
                        con una carga horaria equivalente a {{$curso->cantidad_horas}} horas reloj.
                    </p>
                </div>
                <div class="firmas">
                    <div class="secretaria">
                        <div>
                            {{$nombre_secretaria}}
                            <br>
                            <span style="font-size: 12px;">Secretaria General</span>
                            <br>
                            <span style="font-size: 12px;">Universidad San Ignacio de Loyola</span>
                        </div>
                    </div>
                    <div class="rector">
                        <div>
                            {{$nombre_rector}}
                            <br>
                            <span style="font-size: 12px;">Rector</span>
                            <br>
                            <span style="font-size: 12px;">Universidad San Ignacio de Loyola</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-break"></div>
            <div class="p2-image"></div>
            <div class="p2-content">
                <p class="p2-text">
                    {{$curso->codigo}} {{$curso->llamado_anho}}{{$dato['numero_alumno']}}
                    <br>
                    <br>
                    El presente diploma corresponde a <u>{{Str::title($dato['nombre_alumno'])}}</u>
                    con cédula N°. <u>{{$dato['documento_alumno']}}</u>, que se haya registrado bajo
                    el número de orden <u>{{$dato['numero_orden']}}</u> a fojas <u>{{$dato['pagina']}}</u>
                    del libro de registro de PROGRAMAS aprobados por RCD {{$numero_resolucion}}
                    <br>
                    <br>
                    Fecha: {{$fecha_hoy}}
                </p>
            </div>
        @endforeach
    </body>
    </html>
@endcan
