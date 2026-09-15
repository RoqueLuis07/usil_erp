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
            margin: 1.5cm;
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
        }
        #datos {
            display: inline-block;
            vertical-align: top;
            width: auto;
            height: auto;
            margin-top: 22px;
            text-align: left!important;
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
        .watermark {
            position: absolute;
            top: 50%; /* Ajusta la posición vertical según sea necesario */
            left: 50%;  /* Ajusta la posición horizontal según sea necesario */
            transform: translate(-50% , -50%);
            opacity: 0.2; /* Ajusta la opacidad según sea necesario */
            color: white; /* Ajusta el color según el fondo */
            z-index: -1000;
            pointer-events: none; /* Asegura que la marca de agua no interfiera con los clics */
            width: 100%;
            text-align: center;
        }
    </style>
    @yield('css')
</head>
<body>
    <header>
        <div id="encabezado">
            <div id="logo">
                <img src="{{asset('storage/empresa/' . $empresa->logo)}}" alt="logo-usil" style="width: 100px; height: 100px">
            </div>
            <div id="datos">
                <div style="font-size: 14px"><b>{{$empresa->razon_social}}</b></div>
                <div class="mb-1" style="font-size: 12px"><b>RUC: {{$empresa->ruc}}</b></div>
                <div style="font-size: 10px">{{$empresa->direccion}}</div>
                <div style="font-size: 10px">{{$empresa->ciudad->nombre}}, {{$empresa->pais->nombre}}</div>
                <div style="font-size: 10px">Teléfono: {{$empresa->telefono}}</div>
            </div>
        </div>
        <div class="watermark">
            <img src="{{asset('storage/logos/logo-dark-big.png')}}" alt="logo-usil">
        </div>
    </header>

    <footer>
        <div id="footer-text">
            <span><b>Generado el </b>{{Str::lower($fecha_hoy->locale('es')->translatedFormat('l'))}} {{$fecha_hoy->format('d/m/Y')}} <b>a las</b> {{$fecha_hoy->format('H:i:s')}} <b>por el usuario</b> {{Str::title(Auth::user()->name)}}</span>
        </div>
        <div class="pagenum" id="pagenum"><span>Página </span></div>
    </footer>
    <main>
        @yield('pdf_content')
    </main>
</body>
</html>
