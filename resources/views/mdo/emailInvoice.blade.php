<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de orden</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="text-align: center;">
        <img src="https://mayoristasdeopticas.com/tienda/assets/imgs/logos/logo-ngo.png" alt="Header Image" width="500">
    </div>

    <div style="display: flex; justify-content: center; margin-top: 20px;">
        <table style="width: 80%; max-width: 600px; border-collapse: collapse; margin: 0 auto; text-align: left;">
            <tr>
                <td colspan="2" style="text-align: center; padding: 10px;">
                    <h1 style="text-align: center; font-size: 20px;">
                        Gracias por realizar una compra con nosotros!.
                    </h1>
                    @if($datos['codigoSeguimiento'])
                    <hr style="background-color: #f4f4f4; border: 1px solid #ddd;">
                    <p>Su pedido ha sido enviado!, su tracking number es # {{$datos['codigoSeguimiento']}}</p>
                    <hr style="background-color: #f4f4f4; border: 1px solid #f4f4f4; height: 15px;">
                    @else
                    <hr style="background-color: #f4f4f4; border: 1px solid #ddd;">
                    <p>Su pedido ha sido enviado!</p>
                    <hr style="background-color: #f4f4f4; border: 1px solid #f4f4f4; height: 15px;">
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table style="width: 70%; margin: 0 auto; border-collapse: separate; border-spacing: 0;">
                        <tr>
                            <td colspan="2" style="text-align: left; padding: 10px;">
                                <p style="font-size: 15px; font-weight: bold;">Resumen del pedido:</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; width: 60%; color: #b7b7b7;">Total de artículos:</td>
                            <td style="padding: 8px; color: #b7b7b7;">{{$datos['totalArticulos']}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; color: #b7b7b7;">Subtotal:</td>
                            <td style="padding: 8px; color: #b7b7b7;">${{$datos['subtotal']}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: bold; color: #b7b7b7;">Total:</td>
                            <td style="padding: 8px; font-weight: bold; color: #b7b7b7;">${{$datos['total']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Información del cliente:</td>
                <td style="padding-left: 140px;">Fecha del pedido: {{$datos['fechaPedido']}}</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table style="width: 100%;" border="0" cellspacing="0" cellpadding="10">
                        <tr>
                            <td style="width: 330px;"><strong>Dirección de envío:</strong></td>
                            <td><strong>Método de pago:</strong></td>
                        </tr>
                        <tr>
                            <td rowspan="4" style="width: 200px; color: #b7b7b7;">
                                {{$datos['direccionEnvio']}}
                            </td>
                            <td style="color: #b7b7b7;">{{$datos['metodoPago']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding: 10px;">
                    <div style="text-align: center;">
                        <a href="https://mayoristasdeopticas.com/tienda/login.php" style="
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #354449;
                            color: #ffffff;
                            text-decoration: none;
                            border-radius: 5px;
                            font-weight: bold;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        ">
                            Ingrese a su cuenta
                        </a>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding: 10px;">
                    <div style="
                        background-color: #354449;
                        color: #ffffff;
                        width: 560px;
                        padding: 20px;
                        margin: 50px auto;
                        text-align: center;
                        font-weight: bold;
                        font-size: 14px;
                        border-radius: 0px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    ">
                        <div>2618 NW 112th Ave. Miami, FL, 33172. EE.UU.</div>
                        <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
                        <div>Whatsapp servicio al cliente: +7868000990</div>
                        <div>Ventas: +1 (305) 316-8267</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
