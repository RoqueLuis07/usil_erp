@extends('layouts.pdf.ordenes')
@section('css')
    <style>
        .titulo {
            width: 100%;
            text-align: center;
            font-size: 20px;
        }
        #orden_pago {
            margin-bottom: -1px;
        }
        .tabla {
            width: 100%;
            text-align: center;
            font-size: 12px;
        }
        .tabla-proveedor {
            width: 100%;
            text-align: left;
            font-size: 11px;
        }
        .tabla-concepto {
            width: 100%;
            text-align: left;
            margin-top: 15px;
            font-size: 11px;
        }
        .tabla-pago {
            width: 100%;
            text-align: left;
            margin-top: 15px;
            font-size: 11px;
        }
        .tabla-pagos {
            width: 100%;
            margin-top: 15px;
            font-size: 11px;
        }
        .tabla-pagos th {
            text-align: center;
            background-color: lightgray;
        }
        .tabla-pagos td {
            text-align: center;
        }
        .tabla-firmas {
            width: 100%;
            margin-top: 15px;
            font-size: 11px;
            color: gray;
        }
        .b-top {
            border-top: 1px solid black;
        }
        .b-bottom {
            border-bottom: 1px solid black;
        }
        .b-left {
            border-left: 1px solid black;
        }
        .b-right {
            border-right: 1px solid black;
        }
        .tabla-asiento {
            width: 100%;
            margin-top: 15px;
            font-size: 11px;
        }
    </style>
@endsection

@section('pdf_content')
    <div class="col-12 titulo mb-3">
        <p id="orden_pago"><b>Orden de Pago</b></p>
    </div>
    <div class="tabla">
        <table class="tabla-proveedor">
            <tr>
                <td width="17%">OP N°</td>
                <td width="7%">{{ $orden_pago->id }}</td>
                <td width="8%">Fecha:</td>
                <td width="15%">{{ Carbon\Carbon::parse($orden_pago->created_at)->format('d/m/Y') }}</td>
                <td width="6%">Moneda:</td>
                <td width="20%">{{ $orden_pago->moneda->nombre }}</td>
                @php
                    if ($orden_pago->moneda_id == 1) {
                        $decimales = 0;
                    } else {
                        $decimales = 2;
                    }

                    switch ($orden_pago->tipo) {
                        case 'AN':
                            $tipo = 'Anticipo a Proveedores';
                            break;
                        case 'GE':
                            $tipo = 'Pago de Gerencia';
                            break;
                        default:
                            $tipo = 'Pago a Proveedores';
                            break;
                    }
                @endphp
                <td width="7%">Total:</td>
                <td width="20%">{{ number_format($orden_pago->monto_total, $decimales, ',', '.') }}</td>
            </tr>
            <tr>
                <td>A la Orden de:</td>
                <td colspan="5">{{ $orden_pago->proveedor->razon_social }}</td>
                <td>RUC:</td>
                <td>{{ $orden_pago->proveedor->ruc }}</td>
            </tr>
            <tr>
                <td>Forma de Pago:</td>
                <td colspan="3">{{ $orden_pago->formaPago->nombre }}</td>
                <td>Tipo:</td>
                <td colspan="3">{{ $tipo }}</td>
            </tr>
        </table>
        <table class="tabla-concepto">
            <tr>
                <td width="17%">Concepto:</td>
                <td width="83%">{{ $orden_pago->concepto }}</td>
            </tr>
        </table>
        <table class="tabla-pago">
            <tr>
                @if ($orden_pago->forma_pago_id == 1 || $orden_pago->forma_pago_id == 5)
                    <td width="100%">Caja: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $orden_pago->pago->caja->nombre }}</td>
                @elseif ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7)
                    <td width="17%">Banco:</td>
                    <td width="15%">{{ $orden_pago->pago->cuentaBancaria->banco->nombre }}</td>
                    <td width="18%">
                        @if ($orden_pago->pago->cuentaBancaria->tipo_cuenta == 'CC')
                            Cuenta Corriente
                        @else
                            Caja de Ahorro
                        @endif
                    </td>
                    <td style="text-align: right;" width="5%">Cuenta:</td>
                    <td>{{ $orden_pago->pago->cuentaBancaria->numero_cuenta }}</td>
                    @if ($orden_pago->forma_pago_id == 7)
                        <td width="10%">Cheque N°:</td>
                        <td width="5%">{{ $orden_pago->pago->numero_cheque }}</td>
                    @endif
                @endif
                @if ($orden_pago->pago->fecha)
                    <td width="10%">Fecha:</td>
                    <td width="10%">{{ Carbon\Carbon::parse($orden_pago->pago->fecha)->format('d/m/Y H:i') }}</td>
                @endif
            </tr>
        </table>
        <table class="tabla-pagos">
            <thead>
                <tr>
                    <th colspan="6" style="background-color: white!important; text-align: left!important;">ULTIMOS PAGOS AL PROVEEDOR</th>
                </tr>
                <tr>
                    <th width="10%" style="text-align: left!important"> &nbsp;&nbsp;&nbsp; OP N°</th>
                    <th width="10%">Fecha</th>
                    <th width="10%">Tipo</th>
                    <th width="40%">Detalle</th>
                    <th width="10%">Moneda</th>
                    <th width="20%">Importe</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pagos as $pago)
                    <tr>
                        <td>{{ $pago->ordenPago->id }}</td>
                        <td>{{ Carbon\Carbon::parse($pago->ordenPago->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $pago->ordenPago->tipo }}</td>
                        <td>{{ $pago->ordenPago->concepto }}</td>
                        <td>{{ $pago->ordenPago->moneda->nombre }}</td>
                        <td>{{ number_format($pago->ordenPago->monto_total, $decimales, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No existen pagos realizados al proveedor</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="tabla-firmas">
        <table class="tabla-firmas">
            <tr class="b-top b-right b-left">
                <td class="b-right" width="20%">Hecho por:</td>
                <td class="b-right" width="20%">Verificado por:</td>
                <td class="b-right" width="20%">Aprobado por:</td>
                <td width="40%">Retirado por:</td>
            </tr>
            <tr class="b-bottom b-right b-left">
                <td class="b-right" height="75px;"></td>
                <td class="b-right" height="75px;"></td>
                <td class="b-right" height="75px;"></td>
                <td style="color: black!important;" height="75px;">
                    <br>
                    <p>Nombre:</p>
                    <p>Cédula: </p>
                    <p>Fecha: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Firma: </p>
                </td>
            </tr>
        </table>
    </div>
    @if ($orden_pago->asientoContable && $orden_pago->asientoContable->estado == 'AC')
        <div class="tabla-asiento">
            <table class="tabla-asiento">
                <thead>
                    <tr>
                        <th width="10%">Asiento ID:</th>
                        <th width="7%">{{ $orden_pago->asientoContable->id }}</th>
                        <th width="10%">Fecha:</th>
                        <th width="15%">{{ Carbon\Carbon::parse($orden_pago->asientoContable->fecha)->format('d/m/Y') }}</th>
                        <th width="10%">Moneda:</th>
                        <th width="10%">{{ $orden_pago->asientoContable->moneda->nombre }}</th>
                        <th width="10%">Asiento N°:</th>
                        <th width="10%">{{ $orden_pago->asientoContable->numero }}</th>
                    </tr>
                    <tr>
                        <th>Unidad:</th>
                        <th colspan="3">{{ $orden_pago->asientoContable->unidad_negocio_id }} {{ $orden_pago->asientoContable->unidadNegocio->nombre }}</th>
                        <th>Sub Unidad:</th>
                        <th colspan="2">{{ $orden_pago->asientoContable->subunidad_negocio_id }} {{ $orden_pago->asientoContable->subunidadNegocio->nombre }}</th>
                    </tr>
                    <tr style="background-color: lightgray;">
                        <th colspan="2">Cuenta</th>
                        <th colspan="3">Descripción</th>
                        <th colspan="2" style="text-align: right;">Debe</th>
                        <th style="text-align: right;">Haber</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orden_pago->asientoContable->detalles as $key => $detalle)
                        <tr>
                            <td colspan="2">{{ $detalle->cuentaContable->cuenta }}</td>
                            <td colspan="4">{{ $detalle->cuentaContable->nombre }}
                                <br>
                                <span style="font-size: 8px;">
                                    @if ($key == 0)
                                        {{ $orden_pago->concepto }}
                                    @else
                                        {{ $detalle->descripcion }}
                                    @endif
                                </span>
                            </td>
                            <td style="text-align: right;">{{ number_format($detalle->debe, $decimales, ',' ,'.')}}</td>
                            <td style="text-align: right;">{{ number_format($detalle->haber, $decimales, ',' ,'.')}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: lightgray;">
                        <td colspan="2"></td>
                        <td colspan="4"></td>
                        <td style="text-align: right;">{{ number_format($orden_pago->asientoContable->detalles->sum('debe'), $decimales, ',' ,'.')}}</td>
                        <td style="text-align: right;">{{ number_format($orden_pago->asientoContable->detalles->sum('haber'), $decimales, ',' ,'.')}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
@endsection
