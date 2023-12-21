<html>

<head>
    <link rel="stylesheet" href="{{ public_path('css/pdf/cotizacion.css') }}" />
    <style>
        .header-table {
            width: 700px;
        }


        .logo {
            width: 310px;
            height: 100px;
        }

        .left-table {
            border: 2px solid rgb(0, 0, 0);
            width: 350px;
        }

        .td-left-table {
            width: 350px;
        }


        .right-table {
            border: 2px solid rgb(0, 0, 0);
            width: 340px;
        }

        .td-rigth-table {
            width: 350px;
        }
    </style>
</head>

<body>
    <header>
        <div class="invoice-wrapper">
            <table class="header-table">
                <tr>
                    <td>
                        <table style="border: 2px solid black;">
                            <tr>
                                <td>
                                    MDO INC
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    2618 NW 112th AVENUE.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    MIAMI, FL 33172
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Phone: 305 513 9177 / 305 424 8199
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    TAX ID # 46-0725157
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <img class="logo" src="{{ public_path('mayorista.png') }}" alt="">
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    Fecha:
                                </td>
                                <td>
                                    {{ $cotizacion['fecha'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </header>
    <footer>
    </footer>
    <main>
        <div class="invoice-wrapper">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Cotizacion</th>
                                <th style="text-align: center;">Producto</th>
                                <th style="text-align: center;">Precio</th>
                                <th style="text-align: center;">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($cotizacion['detalles'] as $d)
                                <tr>
                                    <td style="text-align: center;">{{ $d['cotizacion'] }}</td>
                                    <td style="text-align: center;">{{ $d['productoNombre'] }}</td>
                                    <td style="text-align: center;">{{ $d['precio'] }}</td>
                                    <td style="text-align: center;">{{ $d['cantidad'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <!-- /.col -->

            </div>
            <br><br><br>
            <div>
                <table class="header-table">
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Descuento:
                        </td>
                        <td style="text-align:right;">
                            {{ $cotizacion['descuento'] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Subtotal</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S {{ $cotizacion['subTotal'] }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Total</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S {{ $cotizacion['total'] }}</strong>
                        </td>
                    </tr>
                </table>
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
