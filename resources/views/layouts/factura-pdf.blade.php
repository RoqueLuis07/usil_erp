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
            margin-top: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
            font-family: 'Open Sans', sans-serif;
            width: 18.4cm;
            height: 27cm;
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
            height: 115px;
            display: flex;
            align-items: flex-end;
            margin-bottom: 10px;
            border-bottom: 1px solid black;
        }
        #logo {
            width: 110px;
            height: 110px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        #datos {
            display: inline-block;
            vertical-align: top;
            width: 300px;
            height: 110px;
        }
        #titulo {
            display: inline-block;
            vertical-align: top;
            width: 270px;
            height: 110px;
            text-align: right;
            font-style: bold;
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
            width: 684px;
            border-top: 1px solid black;
        }
        .pagenum::after {
            content: counter(page);
        }
        #footer-text {
            width: 600px;
            height: 50px;
            display: inline-block;
            vertical-align: top;
            margin-top: 5px;
        }
        #pagenum {
            width: 80px;
            height: 50px;
            display: inline-block;
            vertical-align: top;
            text-align: right;
            margin-top: 5px;
        }
        main {
            margin-left: -75px;
            margin-top: 60px;
        }
    </style>
    @yield('css')
</head>
<body>
    <header>
        <div id="encabezado">
            <div id="logo">
                <img src="{{asset('storage/empresa/joaquin biedermann.jpg')}}" alt="Toencho Logo" style="width: 100px; height: 100px">
            </div>
            <div id="datos">
                <div class="mb-1" style="font-size: 16px"><b>{{$empresa->nombre_fantasia}}</b></div>
                <div style="font-size: 12px">{{$empresa->razon_social}}</div>
                <div style="font-size: 10px">{{$empresa->direccion}}</div>
                <div style="font-size: 10px">{{$empresa->ciudad->nombre}}, {{$empresa->pais->nombre}}</div>
                <div style="font-size: 10px">Teléfono: {{$empresa->telefono}}</div>
            </div>
            <div id="titulo">
                <span>{{$titulo}}</span>
                <br>
                <span>Nro: {{$numero}}</span>
            </div>
        </div>
    </header>

    <footer>
        <div id="footer-text">
            <?php
                setlocale(LC_ALL, 'spanish');
            ?>
            <span><b>Generado el </b>{{strftime('%A %d de %B del %Y')}} <b>a las</b> {{strftime('%H:%M:%S')}} <b>por el usuario</b> {{Auth::user()->name}}</span>
        </div>
        <div class="pagenum" id="pagenum"><span>Página </span></div>
    </footer>
    <main>
        @yield('pdf_content')
    </main>
</body>
</html>
