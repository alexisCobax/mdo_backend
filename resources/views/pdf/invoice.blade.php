<html>

<head>
    <link rel="stylesheet" href="{{ public_path('css/pdf/factura.css') }}" />
    <style>

        .header-table {
            /* border: 1px solid blue; */
            width: 700px;
        }
/* 
        .header-table tr th,
        .header-table tr td {
            border: 1px solid blue;
        } */

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
                                    <strong>Factura N°:</strong>
                                </td>
                                <td>
                                    8608
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Fecha:
                                </td>
                                <td>
                                    12-Jun-2023
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="header-table">
                <tr>
                    <td class="td-left-table">
                        <table class="left-table" style="border-collapse: collapse;">
                            <tr>
                                <td style="border: 1px solid black;">
                                    <strong>Direccion de cobro:</strong>
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
                    <td class="td-right-table">
                        <table class="right-table" style="border-collapse: collapse;">
                            <tr>
                                <td style="border: 1px solid black;"> 
                                    <strong>Direccion de envio:</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Rolando R Hernandez
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    4634 NW 74TH Avenue
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    SUITE: CKN3918191
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Miami Florida
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    33166, USA
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    TEL876-289-9192
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div style="text-align:center; background-color: rgb(241, 241, 241);"><p>Venta final. No hay devoluciones. Intercambio solamente dentro de las proximas 24hrs de facturada la
                compra</p></div>
            <table class="header-table">
                <tr>
                    <td colspan="3" style="border: 1px solid black;">Cliente:8818-Diagnostics and Eye Health Center</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">Orden #10447</td>
                    <td style="border: 1px solid black;">Envio Via:FEDEX G</td>
                    <td style="border: 1px solid black;">F.O.B.:MIAMI-MDO</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">Fecha Orden:05-Jun-2023</td>
                    <td style="border: 1px solid black;">Vendedor:MDO S</td>
                    <td style="border: 1px solid black;">Terminos: PREPAGO</td>
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
                                <th>Cantidad</th>
                                <th>UPC</th>
                                <th>Descripción/Color/Tamaño/Material</th>
                                <th>Valor</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
                            <td>1</td>
                            <td class="align-center">872016157159</td>
                            <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                            <td>18,99</td>
                            <td>18,99</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="align-center">872016157159</td>
                                <td>4634 NW 74TH Avenue SUITE: CKN3918191</td>
                                <td>18,99</td>
                                <td>18,99</td>
                            </tr>
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
                            Descuento por Promociones:
                        </td>
                        <td style="text-align:right;">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Desc 0%
                        </td>
                        <td style="text-align:right;">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Descuento neto:
                        </td>
                        <td style="text-align:right;">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Subtotal</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S 1.033,4</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            Envio y Manejo:
                        </td>
                        <td style="text-align:right;">
                            14,53
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td style="text-align:right;">
                            <strong>Total</strong>
                        </td>
                        <td style="text-align:right;">
                            <strong>U$S 1.048,00</strong>
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