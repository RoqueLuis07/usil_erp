@can('regenerar_certificados_cursos_ubs')
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
                overflow: hidden;
                font-family: 'Open Sans', sans-serif;
            }
            .page {
                background-size: cover;
                background-position: center;
                page-break-before: always;
                position: relative;
            }
            .p1-content {
                position: relative;
                padding: 20px;
                color: #000;
            }
            .nombre_alumno {
                width: 100%;
                text-align: center;
                margin-top: 32%;
                font-style: bold;
            }
            .curso {
                width: 100%;
                text-align: center;
                margin-top: 0.3em;
            }
            .firmas {
                width: 100%;
                height: 4cm;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                margin-top: 3.6cm;
            }
            .secretaria {
                width: 800px;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 350px;
            }
            .rector {
                display: inline-block;
                vertical-align: top;
                width: 800px;
                height: auto;
                text-align: center;
                border-top: 1px solid black;
                margin-left: 350px;
                position: relative;
            }
            .p2-content {
                position: relative;
                padding: 20px;
                color: #04218C;
                height: 94.96%;
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
        <div style="background-image: url('{{ asset('storage/cursos/certificados/p1.png') }}'); 
                         background-size: cover; 
                         background-position: center; 
                         width: 100%;
						 height: 100%;
                         top: 0; 
                         left: 0;">
            <div class="p1-content">
                <div class="nombre_alumno">
                    <h1>{{$nombre_alumno}}</h1>
                </div>
                <div class="curso">
                    <p>por haber participado en el
                        <br>
                        {{$certificado->curso->nombre_real}}
                        <br>
                        organizado por la Universidad San Ignacio de Loyola, en el período del {{$certificado->inicio}} al {{$certificado->fin}},
                        <br>
                        con una carga horaria equivalente a {{$certificado->curso->cantidad_horas}} horas reloj.
                    </p>
                </div>
                <div class="firmas">
                    <div class="secretaria">
                        <div>
                            {{$nombre_secretaria}}
                            <br>
                            <span>Secretaria General</span>
                            <br>
                            <span>Universidad San Ignacio de Loyola</span>
                        </div>
                    </div>
                    <div class="rector">
                        <div>
                            {{$nombre_rector}}
                            <br>
                            <span>Rector</span>
                            <br>
                            <span>Universidad San Ignacio de Loyola</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page" style="background-image: url('{{ asset('storage/cursos/certificados/p2.png') }}'); background-size: cover; background-position: center;">
            <div class="p2-content">
                <p class="p2-text">
                    {{$certificado->curso->codigo}} {{$certificado->llamado_anho}}{{$numero_alumno}}
                    <br>
                    <br>
                    El presente diploma corresponde a <u>{{Str::title($nombre_alumno)}}</u>
                    con cédula N°. <u>{{number_format($certificado->alumno->numero_documento, 0, ',', '.')}}</u>, que se haya registrado bajo
                    el número de orden <u>{{$certificado->numero_orden}}</u> a fojas <u>{{$certificado->numero_pagina}}</u>
                    del libro de registro de PROGRAMAS aprobados por RCD {{$numero_resolucion}}
                    <br>
                    <br>
                    Fecha: {{$fecha_generacion}}
                </p>
            </div>
        </div>
    </body>
    </html>
@endcan
