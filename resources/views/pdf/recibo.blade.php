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
                    <div style="background-color:rgb(243, 243, 243); width:80%; text-align:center;"><strong>Recibo Nº:
                            {{ $recibo['numero'] }}</strong></div>
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
                    Cliente: {{ $recibo['cliente'] }}
                </td>
                <td style="text-align:right;">
                    Forma de pago: {{ $recibo['formaPago'] }}
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="text-align:left;">
                    Observaciones:
                    {{ $recibo['observaciones'] }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">
                    Total
                    U$S {{ $recibo['total'] }}
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
