<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Módulo aislado para generar HTML de productos y enviarlo a webhooks de GHL.
 * Replica la lógica de templateNuevosArribosPorMarca sin tocar GoHighLevelController.
 */
class GhlTemplateController extends Controller
{
    private const WEBHOOK_URL = 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/3ee8c15e-7d8e-4149-b1a0-76414a16dd08';

    /**
     * Genera el HTML de nuevos arribos por marca y lo envía al webhook de GHL.
     *
     * POST /api/ghl/webhook/nuevos-arribos-por-marca
     * Body: { "marca_id": 359 }
     */
    public function enviarArribosPorMarca(Request $request)
    {
        try {
            $marcaId = $request->input('marca_id') ?? $request->input('marcaId');

            if (empty($marcaId) || (int) $marcaId <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El campo marca_id es requerido y debe ser un número válido.',
                ], 400);
            }

            $marcaId = (int) $marcaId;

            Log::info('GhlTemplateController: Generando template para marca', ['marca_id' => $marcaId]);

            $html = $this->generarHtmlArribosPorMarca($marcaId);

            $payload = ['product' => $html];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => self::WEBHOOK_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($curlError) {
                Log::error('GhlTemplateController: Error cURL', ['error' => $curlError]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de conexión con GHL: ' . $curlError,
                ], 500);
            }

            $responseData = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('GhlTemplateController: Template enviado exitosamente', [
                    'marca_id' => $marcaId,
                    'http_code' => $httpCode,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Template enviado al webhook exitosamente.',
                    'http_code' => $httpCode,
                    'webhook_response' => $responseData,
                    'html_length' => strlen($html),
                ], 200);
            }

            Log::warning('GhlTemplateController: Webhook respondió con error', [
                'marca_id' => $marcaId,
                'http_code' => $httpCode,
                'response' => $response,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'El webhook respondió con error.',
                'http_code' => $httpCode,
                'webhook_response' => $responseData,
            ], 500);

        } catch (\Exception $e) {
            Log::error('GhlTemplateController: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Previsualiza el HTML sin enviarlo al webhook.
     *
     * GET /api/ghl/preview/nuevos-arribos-por-marca?marca_id=359
     */
    public function previewArribosPorMarca(Request $request)
    {
        $marcaId = $request->input('marca_id') ?? $request->input('marcaId');

        if (empty($marcaId) || (int) $marcaId <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'El campo marca_id es requerido.',
            ], 400);
        }

        $html = $this->generarHtmlArribosPorMarca((int) $marcaId);

        if ($request->query('raw') === '1') {
            return response($html, 200)->header('Content-Type', 'text/html');
        }

        return response()->json([
            'status' => 'success',
            'marca_id' => (int) $marcaId,
            'html_length' => strlen($html),
            'html' => $html,
        ], 200);
    }

    /**
     * Genera el HTML completo del email template de nuevos arribos por marca.
     * Replica la misma lógica que GoHighLevelController::templateNuevosArribosPorMarca.
     */
    private function generarHtmlArribosPorMarca(int $marcaId): string
    {
        $urlImagenes = env('URL_IMAGENES_PRODUCTOS');

        $SQL = "SELECT *
                FROM (
                    SELECT
                        producto.id AS productoId,
                        producto.color,
                        producto.nombre AS nombreProducto,
                        marcaproducto.nombre AS nombreMarca,
                        producto.precio,
                        producto.fechaAlta,
                        COALESCE(
                            fotoproducto.url,
                            CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
                        ) AS imagen
                    FROM producto
                    LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
                    LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
                    WHERE producto.stock > 0
                    AND producto.marca = ?
                    ORDER BY producto.id DESC
                    LIMIT 100
                ) AS ultimos
                ORDER BY ultimos.precio ASC";

        $productos = DB::select($SQL, [$marcaId]);

        $html = '<!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Arrivals</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }

            .container {
                max-width: 650px;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 10px;
            }

            .product-item {
                width: calc(33.333% - 10px);
                box-sizing: border-box;
                text-align: center;
                vertical-align: top;
            }

            .product-item img {
                width: 120px;
                height: auto;
                border: 0;
                display: block;
                margin: 0 auto;
            }

            .product-title {
                font-size: 14px;
                font-weight: bold;
                color: #607C8B;
                text-decoration: none;
            }

            .product-description {
                font-size: 12px;
                color: #6C757B;
            }

            .footer {
                background-color: #354449;
                color: #ffffff;
                text-align: center;
                padding: 20px;
                font-size: 14px;
            }

            .footer div {
                margin-bottom: 10px;
            }
        </style>
    </head>

    <body>
        <table class="container" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 650px;">
            <!-- Header -->
            <tr>
                <td>
                    <img src="https://mayoristasdeopticas.com/tienda/assets/imgs/logos/logo-ngo.png" alt="MDO"
                        style="width: 100%; height: auto;">
                </td>
            </tr>
            <tr>
                <td>
                    <a href="https://mayoristasdeopticas.com/tienda/" target="_blank">
                        <img src="https://phpstack-1091339-3819555.cloudwaysapps.com/storage/newArrivalsBanner.png"
                            alt="New Arrivals">
                    </a>
                </td>
            </tr>

            <!-- Productos -->
            <tr>
                <td>';

        $html .= '<table style="width:100%; border-collapse:collapse;">';
        $totalProductos = count($productos);

        $styleRow = 'width: 100%; display: table-row;';
        $styleColumn = 'width: 33.33%; display: table-cell; padding: 10px; text-align: center;';
        $styleImg = 'max-width: 100%; height: auto; display: block; margin: 0 auto;';
        $styleTitle = 'font-size: 16px; font-weight: bold; color: #333; text-decoration: none; margin-top: 8px;';
        $styleDescription = 'font-size: 14px; color: #666; margin: 5px 0; text-align: center;';

        foreach ($productos as $index => $producto) {
            if ($index % 3 === 0) {
                $html .= '<tr style="' . $styleRow . '">';
            }

            $html .= '<td style="' . $styleColumn . '">
                    <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '">
                        <img src="' . $producto->imagen . '" alt="' . htmlspecialchars($producto->nombreProducto) . '" style="' . $styleImg . '" width="120">
                    </a>
                    <br/>
                    <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '" style="' . $styleTitle . '">
                        ' . htmlspecialchars($producto->nombreMarca) . '
                    </a>
                    <br/>
                    <p style="' . $styleDescription . '">' . htmlspecialchars($producto->nombreProducto) . ' | ' . htmlspecialchars($producto->color) . '</p>
                  </td>';

            if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
                $html .= '</tr>';
            }
        }

        $html .= '</table>';

        $html .= '</td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <div>2618 NW 112th Ave. Miami, FL, 33172, EE.UU.</div>
                    <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
                    <div>Whatsapp servicio al cliente: +1(305)  496-5187</div>
                    <div>Ventas: +1 (305) 316-8267</div>
                </td>
            </tr>
                    <tr>
                <td style="text-align:center">
                   <a href="{{email.unsubscribe_link}}">Unsubscribe</a>
                </td>
            </tr>
        </table>

    </body>

    </html>';

        return $html;
    }
}
