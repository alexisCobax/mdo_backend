<html>

<head>
    <link rel="stylesheet" href="{{ public_path('css/pdf/proforma.css') }}" />
    <style>
        .containers {
            width: 300px;
            margin: 20px auto;
            border: 1px solid #ddd;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .labels {
            text-align: left;
        }

        .numbers {
            text-align: right;
        }
    </style>
</head>

<body>
    <header>
        <div class="invoice-wrapper">
            <table class="table">
                <tr>
                    <td class="tax">TAX ID 46-0725157</td>
                    <td class="title">PROFORMA</td>
                    <td class="fob">FOB : Miami</td>
                </tr>
            </table>
            <br /><br />
            <table class="table-info">
                <tr>
                    <td>
                        <div class="">
                            <strong>De:</strong>
                            <table class="table-de">
                                <tr>
                                    <td>
                                        {{ $proforma['tienda']['direccion'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Phone:</strong> {{ $proforma['tienda']['telefono'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Nº pedido:</strong> {{ $proforma['tienda']['numero_pedido'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Fecha de pedido:</strong> {{ $proforma['tienda']['fecha_pedido'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>e-Mail:</strong> f{{ $proforma['tienda']['email'] }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td>
                        <div class="container-table-cliente">
                            &nbsp;&nbsp;&nbsp;<strong>Cliente:</strong>
                            <table class="table-cliente">
                                <tr>
                                    <td>
                                        {{ $proforma['cliente']['nombre'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Nº de Cliente:</strong> {{ $proforma['cliente']['numero'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Tel:</strong> {{ $proforma['cliente']['telefono'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Dirección:</strong> {{ $proforma['cliente']['direccion'] }}
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>
                                        {{$proforma['cliente']['direccion']}}
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td>
                                        <strong>Email:</strong> {{ $proforma['cliente']['email'] }}
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>
                                        {{$proforma['cliente']['email']}}
                                    </td>
                                </tr> --}}
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </header>
    <footer>
        Monto expresado en US Dolar.Precios y Disponibilidad pueden variar sin previo aviso. De ocurrir algún faltante,
        <br />el cliente podrá seleccionar
        un producto de similar valor para su reemplazo. De no encontrar nada de su agrado, podrá dejar el crédito en su
        cuenta para ser utilizado en
        su próxima compra.
        Los paños adicionales a los estuches no se garantizan en ningún caso.
        Cualquier paño que acompañe un estuche es considerado una cortesía extra
        No se aceptan devoluciones, solo cambios dentro de las 24hrs posteriores a la facturacion
    </footer>
    <main>
        <div class="invoice-wrapper">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th class="align-center">Cant</th>
                                <th class="align-center">Código</th>
                                <th class="align-left">Nombre</th>
                                <th class="align-left">Color</th>
                                <th class="align-center">Precio</th>
                                <th class="align-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proforma['detalle'] as $p)
                                <tr>
                                    <td><img src="{{ env('URL_IMAGENES_PRODUCTOS') }}/{{ $p['producto'] }}.jpg"
                                            style="width:90px; height: 70px;" alt="" /></td>
                                    <td class="align-center" style="padding-top:20px;">{{ $p['cantidad'] }}</td>
                                    <td class="align-center" style="padding-top:20px;">{{ $p['codigo'] }}</td>
                                    <td class="align-left" style="padding-top:20px;">{{ $p['nombreProducto'] }}</td>
                                    <td class="align-left" style="padding-top:20px;">{{ $p['nombreColor'] }}</td>
                                    <td class="align-center" style="padding-top:20px;">{{ $p['precio'] }}</td>
                                    <td class="align-center" style="padding-top:20px;">{{ $p['total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 1px solid black;">
                                <td>Cantidad:</td>
                                <td style="text-align:center;">
                                    {{ $proforma['pedido']['cantidad'] }}&nbsp;
                                </td>
                                <td colspan="3";></td>
                                <td style="text-align:right;" colspan="2";>
                                    <strong>U$S {{ $proforma['pedido']['subTotal'] }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.col -->
<br/>
                <table style="width: 700px;">
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Descuento por Promociones:
                        </td>
                        <td style="text-align:right;">
                            {{ $proforma['pedido']['descuentoPromociones'] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Desc {{ $proforma['pedido']['descuentoPorcentual'] }}%
                        </td>
                        <td style="text-align:right;">
                            {{ $proforma['pedido']['descuentoPorcentualTotal'] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Descuento neto:
                        </td>
                        <td style="text-align:right;">
                            {{ $proforma['pedido']['descuentoNeto'] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Subtotal</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S {{ $proforma['pedido']['total'] }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Envio y Manejo:
                        </td>
                        <td style="text-align:right;">
                            {{ $proforma['pedido']['totalEnvio'] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Subtotal con envio</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S {{ $proforma['pedido']['subTotalConEnvio'] }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Credito disponible:
                        </td>
                        <td style="text-align:right;">
                            {{ $proforma['pedido']['creditoDisponible'] }}
                        </td>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Total a abonar</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S {{ $proforma['pedido']['totalAabonar'] }}</strong>
                        </td>
                    </tr>

                </table>

            </div>
            <br><br><br>
            <div>
                <small>Este acuerdo de venta es entre MDO INC. y el cliente que figura en esta factura. El precio de
                    compra mencionado anteriormente debe pagarse a MDO INC.
                    de acuerdo con las instrucciones, de forma prepaga, y antes de la recogida o entrega. El Comprador
                    también deberá realizar el pago correspondiente a los
                    costos de envío de la carga desde el almacén de MOD INC., con excepcion de las cargas que sean
                    retiradas por el transportista del cliente. En ningún caso,
                    MDO INC. o sus agentes, funcionarios y empleados serán responsables de ningún tipo de daño
                    (INCLUYENDO PERO SIN LIMITACIÓN, DAÑOS POR
                    PÉRDIDA DE BENEFICIOS, INTERRUPCIÓN DE NEGOCIOS) derivados del uso o imposibilidad de uso de la
                    mercancía proporcionada. Todos los
                    individuos o entidades entrar en este acuerdo de orden de venta proceden bajo su propio
                    riesgo.</small>
            </div>
        </div>

        </div>

    </main>




    <script type="text/php"> 
    
        if (isset($pdf)) { 
         //Shows number center-bottom of A4 page with $x,$y values
            $x = 250;  //X-axis i.e. vertical position 
            $y = 820; //Y-axis horizontal position
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";  //format of display message
            $font =  $fontMetrics->get_font("helvetica", "bold");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
        </script>
</body>

</html>
