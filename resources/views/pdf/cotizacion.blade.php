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
                    <td class="title">COTIZACIÓN</td>
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
                                        MDO INC
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        2618 NW 112th AVENUE. MIAMI, FL 33172
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone: 305 513 9177 / 305 424 8199
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
                                        {{$cotizacion['nombreCliente']}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Nº de Cliente:</strong> {{$cotizacion['idCliente']}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Tel:</strong> {{$cotizacion['telefonoCliente']}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Dirección:</strong> {{$cotizacion['direccionCliente']}}
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>
                                        {{$proforma['cliente']['direccion']}}
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td>
                                        <strong>Email:</strong> {{$cotizacion['emailCliente']}}
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
        ATENCIÓN: Por favor nota que el costo de envío NO fue cotizado aún. Si no
        tienes un transportista que retire tu carga en nuestro depósito en Miami, por
        favor contáctanos por WhatsApp al +1-786-800-0990 para ayudarte a cotizarlo
        a fin de que puedas realizar un solo pago y así ahorrarte cualquier cargo extra
        de tu banco o Western Union.
    </footer>
    <main>
        <div class="invoice-wrapper">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="text-align: center;">ID</th>
                                <th style="text-align: center;">Codigo</th>
                                <th style="text-align: center;">Producto</th>
                                <th style="text-align: center;">Precio Unit</th>
                                <th style="text-align: center;">Cantidad</th>
                                <th style="text-align: center;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($cotizacion['detalles'] as $d)
                                <tr>
                                    <td style="text-align: center;">{{ $d['productoId'] }}</td>
                                    <td style="text-align: center;">{{ $d['productoCodigo'] }}</td>
                                    <td style="text-align: center;">{{ $d['productoNombre'] }}</td>
                                    <td style="text-align: center;">{{ $d['precio'] }}</td>
                                    <td style="text-align: center;">{{ $d['cantidad'] }}</td>
                                    <td style="text-align: center;">{{ $d['subtotal'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 2px solid black;">
                                <td colspan="4"></td>
                                <td style="text-align:center;">
                                    &nbsp;{{ $cotizacion['cantidad'] }}&nbsp;
                                </td>
                                <td colspan="1";></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.col -->
                <br />
                <table style="width: 700px;">
                    <tr>
                        {{-- <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Total</strong>
                        </td> --}}
                        <td style="text-align:right;">
                            <strong> Total &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;U$S {{ $cotizacion['total'] }}</strong>
                        </td>
                    </tr>

                </table>

            </div>
            <br><br><br>
            <div style="margin: auto; width: 100%;">
                <small style="width: 90%; text-align:center;">Precios y Disponibilidad pueden variar sin previo aviso.<br/>
                    Los paños adicionales a los estuches no se garantizan en ningún caso.<br/>
                    Cualquier paño que acompañe un estuche es considerado una cortesía extra</small>
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
