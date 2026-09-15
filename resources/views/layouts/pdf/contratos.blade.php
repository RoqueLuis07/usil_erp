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
            height: 0.5cm;
            display: flex;
            align-items: flex-end;
            font-size: 10px;
            width: auto;
            border-top: 1px solid black;
        }
        .pagenum {
            width: 20%;
            text-align: center;
        }
        .pagenum::after {
            content: counter(page);
        }
        #footer-text {
            width: 100%;
            display: inline-block;
            vertical-align: top;
            margin-top: 5px;
        }
        .firmas {
            width: 100%;
            text-align: center;
            margin-left: 40px;
        }
        .footer-izquierda {
            width: 20%;
        }
        .footer-medio {
            width: 30%;
        }
        .footer-derecha {
            width: 30%;
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
                <div class="mb-1" style="font-size: 14px"><b>{{$empresa->razon_social}}</b></div>
                {{-- <div style="font-size: 12px">{{$empresa->razon_social}}</div> --}}
                <div style="font-size: 10px">{{$empresa->direccion}}</div>
                <div style="font-size: 10px">{{$empresa->ciudad->nombre}}, {{$empresa->pais->nombre}}</div>
                <div style="font-size: 10px">Teléfono: {{$empresa->telefono}}</div>
            </div>
        </div>
    </header>

    <footer>
        <div id="footer-text">
            <table class="firmas">
                <tbody>
                    <tr>
                        <td class="footer-izquierda">FIRMA DEL INGRESANTE</td>
                        <td class="footer-medio">FIRMA DEL TUTOR/APODERADO</td>
                        <td class="footer-derecha">SELLO Y FIRMA DEL REPRESENTANTE DE ADMISIÓN</td>
                        <td class="pagenum"><span>Página </span></td>
                    </tr>
                    <tr>
                        <td class="footer-izquierda">FECHA _____/_____/_____</td>
                        <td class="footer-medio">FECHA _____/_____/_____</td>
                        <td class="footer-derecha">FECHA _____/_____/_____</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </footer>
    <main>
        @yield('pdf_content')
    </main>
</body>
</html>
