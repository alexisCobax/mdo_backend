<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Services\GoHighLevelService;
use App\Services\TokenManager;


class GoHighLevelController extends Controller
{
    public static function createContact()
    {
        /**
         * payload de ejemplo
         **/
        $payload = [
            "firstName" => "ale5",
            "lastName" => "alexis5",
            "name" => "COBAX5 PRUEBAS5",
            "email" => "ale5@alex5.com",
            "gender" => "male",
            "phone" => "+1 348-182-8888",
            "address1" => "3535 1st St N",
            "city" => "Dolomite",
            "state" => "AL",
            "postalCode" => "35061",
            "website" => "https://www.tesla.com",
            "timezone" => "America/Chihuahua",
            "dnd" => true,
            "source" => "public api",
            "country" => "US",
            "companyName" => "DGS VolMAX",
            "tags" => ["masterlist"]
        ];

        $response = GoHighLevelService::createContact($payload);

        if (isset($response['error']) && $response['error'] === true) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Contacto creado exitosamente.',
            'data' => $response
        ], 200);
    }

    public function templateNuevosArribos(){

        $urlImagenes = env('URL_IMAGENES_PRODUCTOS');


        $SQL = "SELECT
            producto.id AS productoId,
            producto.color,
            producto.nombre AS nombreProducto,
            marcaproducto.nombre AS nombreMarca,
            COALESCE(
                fotoproducto.url,
                CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
            ) AS imagen
        FROM producto
        LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
        LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
        WHERE producto.fechaAlta > '2025-01-10'
        AND producto.proveedorExterno='nywd'
        AND producto.stock > 0
        ORDER BY precio ASC LIMIT 0,100";

        $productos = DB::select($SQL);

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
                width: calc(33.333% - 10px); /* 3 columnas con espacio entre ellas */
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
                    <img src="https://mayoristasdeopticas.com/tienda/assets/imgs/logos/logo-ngo.png" alt="Logo"
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

        $html .= '<table style="width:100%; border-collapse:collapse;">'; // Inicia la tabla principal
        $totalProductos = count($productos);

        // Estilos CSS inline
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
                        <img src="' . $producto->imagen . '" alt="' . $producto->nombreProducto . '" style="' . $styleImg . '" width="120">
                    </a>
                    <br/>
                    <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '" style="' . $styleTitle . '">
                        ' . $producto->nombreMarca . '
                    </a>
                    <br/>
                    <p style="' . $styleDescription . '">' . $producto->nombreProducto . ' | ' . $producto->color . '</p>
                  </td>';

            if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
                $html .= '</tr>';
            }
        }

        $html .= '</table>'; // Cierra la tabla principal

        $html .= '</td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <div>2618 NW 112th Ave. Miami, FL, 33172, EE.UU.</div>
                    <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
                    <div>Whatsapp servicio al cliente: +7868000990</div>
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

    public function enviarNuevosArribos(){
        try {
            // SIEMPRE obtener un token válido ANTES de hacer cualquier llamada a la API de GoHighLevel
            $tokenManager = new TokenManager();
            $accessToken = $tokenManager->getValidToken();

            // Generar el HTML del template
            $html = $this->templateNuevosArribos();

            $payload = [
              "locationId"   => "40UecLU7dZ4KdLepJ7UR",
              "templateId"   => "689e3e8af892621e5c9bbd69",
              "updatedBy"    => "zYy3YOUuHxgomU1uYJty",
              "dnd"          => "{elements:[], attrs:{}, templateSettings:{}}",
              "html"         => $html,  // acá pasamos el string HTML
              "editorType"   => "html",
              "previewText"  => "zYy3YOUuHxgomU1uYJty",
              "isPlainText"  => false
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://services.leadconnectorhq.com/emails/builder/data',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => json_encode($payload), // 🔥 se encarga de escapar el HTML correctamente
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Version: 2021-07-28',
                'Authorization: Bearer ' . $accessToken // ✅ Token automático y válido
              ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode === 200 || $httpCode === 201) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Template actualizado exitosamente',
                    'http_code' => $httpCode,
                    'data' => json_decode($response, true)
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar template',
                    'http_code' => $httpCode,
                    'response' => $response
                ], $httpCode);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function templateNuevosArribosAccesorios($marcaId = null){

        $urlImagenes = env('URL_IMAGENES_PRODUCTOS');

        // Si no se proporciona marcaId, usar 359 por defecto (comportamiento anterior)
        $marcaId = $marcaId ?? 359;
        
        // \Illuminate\Support\Facades\Log::info('GoHighLevel: Generando template para marca', [
        //     'marca_id' => $marcaId
        // ]);

                $SQL = "SELECT *
FROM (
    SELECT
        producto.id AS productoId,
        producto.color,
        producto.nombre AS nombreProducto,
        marcaproducto.nombre AS nombreMarca,
        producto.precio,
        producto.fechaAlta,
        producto.ultimoIngresoDeMercaderia,
        COALESCE(
            fotoproducto.url,
            CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
        ) AS imagen
    FROM producto
    LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
    LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
    WHERE producto.stock > 0
    AND producto.borrado IS NULL
    AND producto.grupo = 7
    AND producto.suspendido = 0
    ORDER BY producto.id ASC
    LIMIT 100
) AS ultimos
ORDER BY 
    nombreMarca ASC,
    ultimoIngresoDeMercaderia ASC,
    productoId ASC;";

                    // $SQL = "SELECT *
                    // FROM (
                    //     SELECT
                    //         producto.id AS productoId,
                    //         producto.color,
                    //         producto.nombre AS nombreProducto,
                    //         marcaproducto.nombre AS nombreMarca,
                    //         producto.precio,
                    //         producto.fechaAlta,
                    //         COALESCE(
                    //             fotoproducto.url,
                    //             CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
                    //         ) AS imagen
                    //     FROM producto
                    //     LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
                    //     LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
                    //     WHERE producto.proveedorExterno='nywd'
                    //     AND producto.stock > 0
                    //     AND marcaproducto.nombre LIKE '%GUESS%'
                    //     ORDER BY producto.fechaAlta DESC
                    //     LIMIT 100
                    // ) AS ultimos
                    // ORDER BY ultimos.precio ASC;";

        $productos = DB::select($SQL, [$marcaId]);

        $html = '<!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>20% OFF Accesorios</title>
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
                width: calc(33.333% - 10px); /* 3 columnas con espacio entre ellas */
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
                        <img style="width: 800px; height: 533px;" src="https://phpstack-1091339-3819555.cloudwaysapps.com/storage/email20offaccesorios.png"
                            alt="20% OFF Accesorios">
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <h2 style="text-align:center; color:rgb(255, 0, 0);">CODIGO DE DESCUENTO:</h2><h1 style="text-align:center; color:rgb(0, 0, 0);"> PROMOACC2026</h1>
                </td>
            </tr>
            <!-- Productos -->
            <tr>
                <td>';

        $html .= '<table style="width:100%; border-collapse:collapse;">'; // Inicia la tabla principal
        $totalProductos = count($productos);

        // Estilos CSS inline
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
                        <img src="' . $producto->imagen . '" alt="' . $producto->nombreProducto . '" style="' . $styleImg . '" width="120">
                    </a>
                    <br/>
                    <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '" style="' . $styleTitle . '">
                        ' . $producto->nombreMarca . '
                    </a>
                    <br/>
                    <p style="' . $styleDescription . '">' . $producto->nombreProducto . ' | ' . $producto->color . '</p>
                  </td>';

            if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
                $html .= '</tr>';
            }
        }

        $html .= '</table>'; // Cierra la tabla principal

        $html .= '</td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <div>2618 NW 112th Ave. Miami, FL, 33172, EE.UU.</div>
                    <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
                    <div>Whatsapp servicio al cliente: +1(305) 496-5187</div>
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

    public function templateNuevosArribosPorMarcaDiaDeLaMadre($marcaId = null){

        $urlImagenes = env('URL_IMAGENES_PRODUCTOS');
        $marcaId = $marcaId ?? 359;
    
        // $SQL = "SELECT *
        //     FROM (
        //         SELECT
        //             producto.id AS productoId,
        //             producto.color,
        //             producto.nombre AS nombreProducto,
        //             marcaproducto.nombre AS nombreMarca,
        //             producto.precio,
        //             producto.fechaAlta,
        //             COALESCE(
        //                 fotoproducto.url,
        //                 CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
        //             ) AS imagen
        //         FROM producto
        //         LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
        //         LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
        //         WHERE producto.stock > 0
        //         AND producto.marca = ?
        //         ORDER BY producto.id DESC
        //         LIMIT 100
        //     ) AS ultimos
        //     ORDER BY ultimos.precio ASC";

        $SQL = "SELECT *
FROM (
    SELECT
        producto.id AS productoId,
        producto.color,
        producto.nombre AS nombreProducto,
        marcaproducto.nombre AS nombreMarca,
        producto.precio,
        producto.fechaAlta,

        CONCAT(
            'https://phpstack-1091339-3819555.cloudwaysapps.com/storage/app/public/images/',
            COALESCE(
                IF(producto.proveedorExterno = 'nywd',
                    IF(
                        fotoproducto.descargada = 2,
                        SUBSTRING_INDEX(fotoproducto.url, '/', -1),
                        '0.jpg'
                    ),
                    CONCAT(producto.imagenPrincipal, '.jpg')
                ),
                '0.jpg'
            )
        ) AS imagen

    FROM producto
    LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
    LEFT JOIN categoriaproducto ON producto.categoria = categoriaproducto.id
    LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal

    WHERE
        producto.precioPromocional > 0
        AND producto.stock > 0
        AND producto.suspendido = 0
        AND (producto.destacado = 1 OR producto.nuevo = 1)
        AND producto.borrado IS NULL

    ORDER BY
        CASE
            WHEN producto.marca IN (359,789,797,800,787) THEN 0
            ELSE 3
        END ASC,
        marcaproducto.nombre ASC,
        producto.ultimoIngresoDeMercaderia DESC,
        producto.id ASC

) AS productos
ORDER BY productos.precio ASC;";
    
        $productos = DB::select($SQL);
    
        $html = '<!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo Día de la Madre</title>
    </head>
    
    <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, sans-serif;">
    
    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width:750px; background:#ffffff;">
    
    <!-- LOGO -->
    <tr>
    <td style="text-align:center;">
    <img src="https://mayoristasdeopticas.com/tienda/assets/imgs/logos/logo-ngo.png" style="width:100%;">
    </td>
    </tr>
    
    <!-- BANNER PROMO -->
    <tr>
    <td style="
    background-image:url(\'https://images.unsplash.com/photo-1511499767150-a48a237f0083?q=80&w=1200\');
    background-size:cover;
    background-position:center;
    text-align:center;
    padding:40px 20px;
    ">
    
    <div style="background:rgba(255,255,255,0.88); padding:25px;">
    
    <h2 style="color:#e91e63;">🌷 PROMO DÍA DE LA MADRE 🌷</h2>
    
    <p><strong>10% OFF en TODA la tienda virtual</strong></p>
    <p>Solo en mayo - Mayoristas de Ópticas</p>
    
    <p>✅ Todas las marcas</p>
    <p>✅ Todos los modelos</p>
    <p>✅ Cuenta comercial activa</p>
    
    <p><strong>Pide tu cupón antes de finalizar tu pedido</strong></p>
    
    <a href="https://mayoristasdeopticas.com/tienda/marcas.php"
    style="display:inline-block; padding:12px 20px; background:#e91e63; color:#fff; text-decoration:none; border-radius:5px;">
    Ir a la tienda
    </a>
    
    <p style="margin-top:15px;">📲 +1 (786) 800-0990</p>
    
    </div>
    
    </td>
    </tr>
    
    <!-- PRODUCTOS -->
    <tr>
    <td>';
    
        $html .= '<table style="width:100%; border-collapse:collapse;">';
    
        $totalProductos = count($productos);
    
        $styleColumn = 'width: 33.33%; padding: 15px 10px; text-align: center;';
        $styleImg = 'max-width: 100%; height: auto; display: block; margin: 0 auto;';
        $styleTitle = 'font-size: 14px; font-weight: bold; color: #333; text-decoration: none;';
        $styleDescription = 'font-size: 12px; color: #666; margin: 5px 0;';
    
        foreach ($productos as $index => $producto) {
    
            if ($index % 3 === 0) {
                $html .= '<tr>';
            }
    
            $html .= '<td style="'.$styleColumn.'">
                <a href="https://mayoristasdeopticas.com/tienda/producto.php?id='.$producto->productoId.'">
                    <img src="'.$producto->imagen.'" alt="'.$producto->nombreProducto.'" style="'.$styleImg.'" width="140">
                </a>
                <br/>
                <a href="https://mayoristasdeopticas.com/tienda/producto.php?id='.$producto->productoId.'" style="'.$styleTitle.'">
                    '.$producto->nombreMarca.'
                </a>
                <br/>
                <p style="'.$styleDescription.'">
                    '.$producto->nombreProducto.' | '.$producto->color.'
                </p>
            </td>';
    
            if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
                $html .= '</tr>';
            }
        }
    
        $html .= '</table>';
    
        $html .= '</td>
    </tr>
    
    <!-- FOOTER -->
    <tr>
    <td style="background:#354449; color:#ffffff; text-align:center; padding:20px; font-size:14px;">
    <div>2618 NW 112th Ave. Miami, FL, 33172, EE.UU.</div>
    <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
    <div>Whatsapp servicio al cliente: +1(305) 496-5187</div>
    <div>Ventas: +1 (305) 316-8267</div>
    </td>
    </tr>
    
    <tr>
    <td style="text-align:center; padding:10px;">
    <a href="{{email.unsubscribe_link}}">Unsubscribe</a>
    </td>
    </tr>
    
    </table>
    </body>
    </html>';
    
        return $html;
    }

    public function templateNuevosArribosPorMarca($marcaId = null){

        $urlImagenes = env('URL_IMAGENES_PRODUCTOS');

        // Si no se proporciona marcaId, usar 359 por defecto (comportamiento anterior)
        $marcaId = $marcaId ?? 359;
        
        // \Illuminate\Support\Facades\Log::info('GoHighLevel: Generando template para marca', [
        //     'marca_id' => $marcaId
        // ]);

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

                    // $SQL = "SELECT *
                    // FROM (
                    //     SELECT
                    //         producto.id AS productoId,
                    //         producto.color,
                    //         producto.nombre AS nombreProducto,
                    //         marcaproducto.nombre AS nombreMarca,
                    //         producto.precio,
                    //         producto.fechaAlta,
                    //         COALESCE(
                    //             fotoproducto.url,
                    //             CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
                    //         ) AS imagen
                    //     FROM producto
                    //     LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
                    //     LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
                    //     WHERE producto.proveedorExterno='nywd'
                    //     AND producto.stock > 0
                    //     AND marcaproducto.nombre LIKE '%GUESS%'
                    //     ORDER BY producto.fechaAlta DESC
                    //     LIMIT 100
                    // ) AS ultimos
                    // ORDER BY ultimos.precio ASC;";

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
                width: calc(33.333% - 10px); /* 3 columnas con espacio entre ellas */
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

        $html .= '<table style="width:100%; border-collapse:collapse;">'; // Inicia la tabla principal
        $totalProductos = count($productos);

        // Estilos CSS inline
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
                        <img src="' . $producto->imagen . '" alt="' . $producto->nombreProducto . '" style="' . $styleImg . '" width="120">
                    </a>
                    <br/>
                    <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '" style="' . $styleTitle . '">
                        ' . $producto->nombreMarca . '
                    </a>
                    <br/>
                    <p style="' . $styleDescription . '">' . $producto->nombreProducto . ' | ' . $producto->color . '</p>
                  </td>';

            if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
                $html .= '</tr>';
            }
        }

        $html .= '</table>'; // Cierra la tabla principal

        $html .= '</td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <div>2618 NW 112th Ave. Miami, FL, 33172, EE.UU.</div>
                    <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
                    <div>Whatsapp servicio al cliente: +1(305) 496-5187</div>
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

    public function enviarNuevosArribosPorMarca(\Illuminate\Http\Request $request = null){
        try {
            // Obtener el ID de marca del request
            $marcaId = null;
            if ($request) {
                // Intentar obtener desde JSON body primero, luego desde input (form data)
                // Laravel parsea automáticamente el JSON cuando Content-Type es application/json
                // Usar input() que funciona tanto para JSON como para form data
                $marcaId = $request->input('marca_id') ?? $request->input('marcaId');
                
                // Si no está en input, intentar desde json()
                if ($marcaId === null && $request->isJson()) {
                    $jsonData = $request->json()->all();
                    $marcaId = $jsonData['marca_id'] ?? $jsonData['marcaId'] ?? null;
                }
                
                // Convertir a entero si es string
                if ($marcaId !== null) {
                    $marcaId = (int) $marcaId;
                }
                
                \Illuminate\Support\Facades\Log::info('GoHighLevel: Recibiendo marca_id', [
                    'input_marca_id' => $request->input('marca_id'),
                    'input_marcaId' => $request->input('marcaId'),
                    'json_all' => $request->isJson() ? $request->json()->all() : null,
                    'all_data' => $request->all(),
                    'marca_id' => $marcaId,
                    'request_method' => $request->method(),
                    'content_type' => $request->header('Content-Type'),
                    'is_json' => $request->isJson(),
                    'raw_content' => substr($request->getContent(), 0, 200)
                ]);
                
                // Si se llama desde la API (con request) y no hay marca_id, es un error
                if (!$marcaId || $marcaId <= 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El ID de marca es requerido y debe ser un número válido',
                        'debug' => [
                            'input_marca_id' => $request->input('marca_id'),
                            'input_marcaId' => $request->input('marcaId'),
                            'json_data' => $request->isJson() ? $request->json()->all() : null,
                            'all_data' => $request->all(),
                            'request_body' => $request->getContent(),
                            'marca_id_received' => $marcaId,
                            'content_type' => $request->header('Content-Type')
                        ]
                    ], 400);
                }
            }
            // Si no hay request (llamado desde consola), marcaId será null y usará el valor por defecto

            \Illuminate\Support\Facades\Log::info('GoHighLevel: Enviando nuevos arribos por marca', [
                'marca_id' => $marcaId
            ]);

            // SIEMPRE obtener un token válido ANTES de hacer cualquier llamada a la API de GoHighLevel
            $tokenManager = new TokenManager();
            $accessToken = $tokenManager->getValidToken();

            // Generar el HTML del template con los productos de la marca
            $html = $this->templateNuevosArribosPorMarca($marcaId);

            $payload = [
              "locationId"   => "40UecLU7dZ4KdLepJ7UR",
              "templateId"   => "68c9bd96d3ca6ed62facf978",
              "updatedBy"    => "zYy3YOUuHxgomU1uYJty",
              "dnd"          => "{elements:[], attrs:{}, templateSettings:{}}",
              "html"         => $html,  // acá pasamos el string HTML
              "editorType"   => "html",
              "previewText"  => "zYy3YOUuHxgomU1uYJty",
              "isPlainText"  => false
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://services.leadconnectorhq.com/emails/builder/data',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => json_encode($payload), // 🔥 se encarga de escapar el HTML correctamente
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Version: 2021-07-28',
                'Authorization: Bearer ' . $accessToken // ✅ Token automático y válido
              ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode === 200 || $httpCode === 201) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Template actualizado exitosamente',
                    'http_code' => $httpCode,
                    'data' => json_decode($response, true)
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar template',
                    'http_code' => $httpCode,
                    'response' => $response
                ], $httpCode);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar consulta de agente de venta a GoHighLevel
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarConsultaAgente(\Illuminate\Http\Request $request)
    {
        try {
            // Validar los datos recibidos
            $request->validate([
                'email' => 'required|email',
                'whatsapp' => 'required|string'
            ]);

            $email = $request->input('email');
            $whatsapp = $request->input('whatsapp');
            
            // Email CCO hardcodeado
            $emailCCO = 'alexiscobax1@gmail.com';

            // Preparar el payload para GoHighLevel
            $payload = [
                'whatsapp' => $whatsapp,
                'emailCCO' => $emailCCO,
                'email' => $email
            ];

            // Enviar a GoHighLevel
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/xPBWttgCtic4S9A4hY2b',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($curlError) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al conectar con GoHighLevel: ' . $curlError
                ], 500);
            }

            $responseData = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Consulta enviada exitosamente',
                    'data' => $responseData
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al enviar la consulta a GoHighLevel',
                    'http_code' => $httpCode,
                    'response' => $responseData
                ], $httpCode);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
