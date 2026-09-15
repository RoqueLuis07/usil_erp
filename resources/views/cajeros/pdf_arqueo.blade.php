@can('ver_arqueos_cajas_cajero')
    @extends('layouts.pdf.reportes')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
            #titulo {
                margin-bottom: -1px;
            }
            .div-resumen {
                width: 100%;
                text-align: center;
            }
            #resumen {
                margin: 0 auto;
                width: 75%;
                margin-top: 1cm;
                border: 1px solid black;
            }
            #resumen thead{
                text-align: center;
                background-color: #BFBFBF;
                color: black;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            #resumen td{
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
            .monto {
                text-align: center;
            }
            .div-movimientos {
                width: 100%;
                text-align: center;
            }
            #movimientos {
                margin: 0 auto;
                width: 100%;
                margin-top: 1cm;
                border: 1px solid black;
            }
            #movimientos thead{
                text-align: center;
                background-color: #BFBFBF;
                color: black;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            #movimientos td{
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
            #saldo_caja {
                margin-top: 2em;
            }
            #firmas {
                position: fixed;
                width: 90%;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                margin-top: 5cm;
            }
            #firma {
                width: 200px;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 50px;
            }
            #recibe {
                display: inline-block;
                vertical-align: top;
                width: 200px;
                height: auto;
                text-align: center;
                border-top: 1px solid black;
                margin-left: 50px;
            }
            #aclaracion {
                display: inline-block;
                vertical-align: top;
                width: 250px;
                height: auto;
                text-align: left!important;
                margin-left: 50px;
                font-size: 14px;
            }
            .linea_aclaracion {
                display: inline-block;
                width: 110px;
                border-bottom: 1px solid black;
                vertical-align: middle;
                margin-left: 10px;
                margin-top: 15px;
            }
            .linea_ci {
                display: inline-block;
                width: 145px;
                border-bottom: 1px solid black;
                vertical-align: middle;
                margin-left: 10px;
                margin-top: 15px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-3">
            <p><b>ARQUEO DE CAJA</b></p>
        </div>
        <div class="col-12 mb-3">
            <p>
                <span><b>Fecha: </b> {{$fecha_arqueo}}</span>
                <br>
                <span><b>Caja: </b> {{$caja->nombre}}</span>
            </p>
        </div>
        <div class="div-resumen">
            <table id="resumen">
                <thead>
                    <tr>
                        <td colspan="2">Resumen</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ventas</td>
                        <td class="monto">{{number_format($ventas, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Efectivo</td>
                        <td class="monto">{{number_format($efectivo, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Tarjetas de Débito</td>
                        <td class="monto">{{number_format($tarjetas_debitos, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Tarjetas de Crédito</td>
                        <td class="monto">{{number_format($tarjetas_creditos, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Transferencias</td>
                        <td class="monto">{{number_format($transferencias, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Depósitos</td>
                        <td class="monto">{{number_format($depositos, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Otros Ingresos</td>
                        <td class="monto">{{number_format($otros_ingresos, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Compras</td>
                        <td class="monto" style="color: #F7564D">{{number_format($compras, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Gastos</td>
                        <td class="monto" style="color: #F7564D">{{number_format($gastos, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Otros Egresos</td>
                        <td class="monto" style="color: #F7564D">{{number_format($otros_egresos, 0, ',', '.')}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right;"><b>Total Ingresos:</b></td>
                        <td class="monto"><b>{{number_format($total_ingresos, 0, ',', '.')}}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; color: #F7564D"><b>Total Egresos:</b></td>
                        <td class="monto" style="color: #F7564D"><b>{{number_format($total_egresos, 0, ',', '.')}}</b></td>
                    </tr>
                    <tr style="background-color: #BFBFBF">
                        <td style="text-align: right;"><b>Total:</b></td>
                        <td class="monto"><b>{{number_format($total_resumen, 0, ',', '.')}}</b></td>
                    </tr>
                </tfoot>
            </table>
            <div id="saldo_caja">
                <p><b>Monto en Caja: </b>{{number_format($caja->monto, 0, ',', '.')}}</p>
            </div>
        </div>
        <div id="firmas">
            <div id="firma">
                <div style="font-size: 14px"><b>{{Str::title(Auth::user()->name)}}</b></div>
            </div>
            <div id="recibe">
                <div style="font-size: 14px">
                    <b>Recibido por</b>
                </div>
                <br>
                <div id="aclaracion">
                    <b>Aclaración:</b>
                    <div class="linea_aclaracion"></div>
                    <br><br>
                    <b>C.I.N°:</b>
                    <div class="linea_ci"></div>
                </div>
            </div>
        </div>
        <div style="page-break-after:always"></div>
        <div class="div-movimientos">
            <table id="movimientos">
                <thead>
                    <tr>
                        <td colspan="3">Movimientos</td>
                    </tr>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 30%">Descripcion</th>
                        <th style="width: 10%">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $key => $movimiento)
                        <tr @if ($movimiento->estado == 'IN') style="color: #F7564D" @endif>
                            <td class="monto">{{$key + 1}}</td>
                            <td>
                                @if ($movimiento->tipo_movimiento_id == 12)
                                    FACTURA N°: {{$movimiento->venta->numero_factura}}
                                @elseif ($movimiento->tipo_movimiento_id == 1 || $movimiento->tipo_movimiento_id == 2)
                                    MOVIMIENTO DE {{$movimiento->tipoMovimiento->nombre}} - N°: {{$movimiento->id}}
                                @elseif ($movimiento->tipo_movimiento_id == 3)
                                    MOVIMIENTO {{$movimiento->tipoMovimiento->nombre}} - N°: {{$movimiento->id}}
                                @endif
                            </td>
                            <td class="monto" @if (in_array($movimiento->tipo_movimiento_id, [2, 3])) style="color: #F7564D" @endif>@if ($movimiento->estado != 'IN') {{number_format($movimiento->monto, 0, ',', '.')}} @else ANULADO @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endsection
@endcan
