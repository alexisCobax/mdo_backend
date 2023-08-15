<!DOCTYPE html>
<html>

<head>
    <style>
        #tabla-contenedor {
            border: 2px solid black;
            width: 100%;
            margin: 0 auto;
        }

        #tabla-contenedor table {
            width: 100%;
            border-collapse: collapse;
        }

        #tabla-contenedor td {
            text-align: center;
            padding: 10px;
            /* border: 1px solid black; */
        }

        .logo {
            width: 260px;
            height: 60px;
        }

        body {
    margin: 6cm 1cm 5cm;
}

    </style>
</head>

<body>
    <div id="tabla-contenedor">
        <table>
            <tr>
                <td style="text-align:left;">
                    <div style="background-color:rgb(243, 243, 243); width:80%; text-align:center;"><strong>Recibo Nº: 8050</strong></div>
                </td>
                <td>
                    <img class="logo" src="{{ public_path('mayorista.png') }}" alt="">
                </td>
                <td style="text-align:right;">
                    12-Jun-2023
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="text-align:left;">
                    Cliente: Diagnostics and Eye Health Center (8818)
                </td>
                <td style="text-align:right;">
                    Forma de pago: Transferencia Bancaria
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="text-align:left;">
                    Observaciones:
                    ORIG.DIAGNOSTIC CENTRAL AVENUE SOUTHDALE PLACA, JM, JAMAICA
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">
                    Total
                    U$S 1.048,00
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
