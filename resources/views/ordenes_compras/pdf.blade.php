@extends('layouts.pdf.ordenes')
@section('css')
    <style>
        .titulo {
            width: 100%;
            text-align: center;
            font-size: 20px;
        }
        #orden_compra {
            margin-bottom: -1px;
        }
        .tabla {
            width: 100%;
            text-align: center;
            font-size: 12px;
        }
        .tabla-proveedor {
            width: 50%;
            float: left;
            margin-bottom: 15px;
        }
        .tabla-proveedor td.bordered {
            padding: 5px;
            border: 1px solid black;
            height: 75px;
            text-align: left;
        }
        .small {
            font-size: 10px;
        }
        .tabla-proveedor-otros {
            width: 50%;
            float: right;
            margin-bottom: 15px;
        }
        .tabla-proveedor-otros td.bordered {
            padding: 5px;
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            border-top: 1px solid black;
            height: 42.1px;
            text-align: left;
        }
        .tabla-2 {
            width: 99.68%;
            margin-top: 105px;
            border-right: 1px solid black;
            border-bottom: 1px solid black;
            border-left: 1px solid black;
            font-size: 12px;
        }
        .tabla-3 {
            width: 100%;
            margin-top: 10px;
        }
        .tabla-detalles {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-detalles thead {
            font-size: 10px;
            text-align: center;
            font-style: bold;
            background-color: #002663;
            border: 1px solid black;
            color: white;
        }
        .tabla-detalles tbody td {
            font-size: 10px;
            height: 20px;
            border: 1px solid black;
            border-collapse: collapse;
        }
        .tabla-detalles tfoot {
            width: 100%;
            font-size: 10px;
            text-align: center;
            font-style: bold;
        }
        .observaciones {
            width: 100%;
            margin-top: 50px;
            border: 1px solid black;
            font-size: 10px;
            height: 100px;
        }
        .nombres {
            width: 100%;
            margin-top: 150px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
    </style>
@endsection

@section('pdf_content')
    <div class="col-12 titulo mb-3">
        <p id="orden_compra"><b>Orden de Compra N° {{$orden_compra->id}}</b></p>
    </div>
    <div class="tabla">
        <table class="tabla-proveedor">
            <tr>
                <td style="font-size: 14px;">Señores</td>
            </tr>
            <tr>
                <td class="bordered">
                    <span style="font-weight: bold; font-size: 12px;">{{ $orden_compra->proveedor->razon_social }}</span>
                    <br>
                    <span class="small">
                        {{ $orden_compra->proveedor->direccion }}
                        <br>
                        {{ $orden_compra->proveedor->ciudad->nombre }}
                    </span>
                </td>
            </tr>
        </table>
        <table class="tabla-proveedor-otros">
            <tr>
                <td style="font-size: 12px; height: 50px; vertical-align: top;">
                    <span style="margin-left: 10px;">
                        Teléfono: &nbsp;&nbsp;&nbsp;&nbsp; {{ $orden_compra->proveedor->telefono }}
                    </span>
                    <br>
                    <span style="margin-left: 10px;">
                        Correo: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $orden_compra->proveedor->email }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="bordered" style="text-align: center;">
                    <span style="font-weight: bold; font-size: 12px;">R.U.C.</span>
                    <br>
                    <span class="small">{{ $orden_compra->proveedor->ruc }}</span>
                </td>
                <td class="bordered" style="text-align: center;">
                    <span style="font-weight: bold; font-size: 12px;">Ciudad</span>
                    <br>
                    <span class="small">{{ $orden_compra->proveedor->ciudad->nombre }}</span>
                </td>
            </tr>
        </table>
    </div>
    <div class="tabla-2">
        <table>
            <tr>
                <td style="width: 410.1px; border-right: 1px solid black; font-style: bold">
                    Forma de Pago
                </td>
                <td style="font-style: bold; text-align: center; width: 175px;">
                    Fecha del Pedido
                </td>
            </tr>
                <tr>
                    <td style="width: 410.1px; border-right: 1px solid black; text-align: center;">
                        {{ $orden_compra->condicion_compra == 'CR' ? $orden_compra->credito_a . ' DÍAS FECHA FACTURA' : 'CONTADO' }}
                    </td>
                    <td style="text-align: center; width: 175px;">
                        {{ Carbon\Carbon::parse($orden_compra->created_at)->format('d  |  m  |  Y') }}
                    </td>
                </tr>
        </table>
    </div>
    <div class="tabla-3">
        <table class="tabla-detalles">
            <thead>
                <tr>
                    <th style="width: 50%;">Descripción</th>
                    <th style="width: 10%;">Cant.</th>
                    <th style="width: 20%;">Precio Unitario</th>
                    <th style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden_compra->detalles as $detalle)
                    <tr>
                        @php
                            $decimales = 0;
                            if ($orden_compra->moneda_id == 2) {
                                $decimales = 2;
                            }

                            $total_filas = 15;
                        @endphp
                        <td><span style="margin-left: 5px;">
                                @if ($detalle->descripcion)
                                    {{ $detalle->descripcion }}
                                @else
                                    {{ $detalle->articulo->nombre }}
                                @endif
                            </span></td>
                        <td style="text-align: center;">{{ number_format($detalle->cantidad, 0, ',', '.') }}</td>
                        <td style="text-align: right;"><span style="margin-right: 5px;">{{ number_format($detalle->precio_costo, $decimales, ',', '.') }}</span></td>
                        <td style="text-align: right;"><span style="margin-right: 5px;">{{ number_format($detalle->subtotal, $decimales, ',', '.') }}</span></td>
                    </tr>
                    @for ($i = $orden_compra->detalles->count(); $i < $total_filas; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor

                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                    <td style="text-align: right;"><span style="margin-right: 5px;">{{ number_format($orden_compra->monto_total, $decimales, ',', '.') }}</span></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Moneda</strong></td>
                    <td style="text-align: right;"><span style="margin-right: 5px;">{{ $orden_compra->moneda->nombre }}</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="observaciones">
        <span style="font-weight: bold; margin-left: 5px;">Observaciones para el Proveedor</span>
        <br><br>
        <span style="margin-left: 5px; ">{{ $orden_compra->observaciones == null ? 'SIN OBSERVACIONES' : $orden_compra->observaciones }}</span>
    </div>
    <div class="nombres">
        <table style="width: 100%;">
            <tr style="border-bottom: 1px solid black; text-align: center; font-weight: bold;">
                <td style="width: 33.3%;">@if ($orden_compra->actualizadoPor) {{ $orden_compra->actualizadoPor->name }} @endif</td>
                <td style="width: 33.3%;"></td>
                <td style="width: 33.3%;">{{ $orden_compra->cargadoPor->name }}</td>
            </tr>
            <tr style="text-align: center; font-weight: bold;">
                <td>APROBADO POR (Nombre y Firma)</td>
                <td>PROVEEDOR</td>
                <td>CREADO POR (Nombre y Firma)</td>
            </tr>
        </table>
    </div>
@endsection
