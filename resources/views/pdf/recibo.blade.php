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
        <table>
            <tr>
                <td style="text-align: justify;">
                    <h4>Deposito en concepto de seña</h4>
                    "El comprador deberá abonar el saldo de su compra dentro de los 30 (treinta) 
                    días a contar desde la fecha sin necesidad de ningún requerimiento. En el caso 
                    de que el COMPRADOR no abonara el saldo de precio dentro del plazo establecido 
                    incurrirá en mora de pleno derecho por el mero vencimiento del plazo pactado y 
                    automáticamente, sin necesidad de requerimiento alguno, el vendedor queda facultado
                     para dar por rescindido sin más tramite y de pleno derecho el contrato, sin necesidad 
                     de intervención judicial alguna, quedando a su exclusivo beneficio la suma percibida como seña."

                </td>
            </tr>
        </table>
    </div>
</body>

</html>
