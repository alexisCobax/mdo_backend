<?php

namespace App\Transformers\Pdf;

use App\Models\Cliente;
use App\Helpers\DateHelper;
use App\Models\Fotoproducto;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Log;

class FindByIdTransformer extends TransformerAbstract
        {private function reducirImagen($imageData, $maxWidth = 300, $quality = 70)
        {
            $img = @imagecreatefromstring($imageData);
            if (!$img) {
                return null;
            }

            $width  = imagesx($img);
            $height = imagesy($img);

            // Si ya es chica, no tocarla
            if ($width <= $maxWidth) {
                return $imageData;
            }

            $newWidth  = $maxWidth;
            $newHeight = intval($height * ($maxWidth / $width));

            $tmp = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            ob_start();
            imagejpeg($tmp, null, $quality); // fuerza JPEG comprimido
            $output = ob_get_clean();

            imagedestroy($img);
            imagedestroy($tmp);

            return $output;
        }
    public function transform($pedido)
    {
        $tiempoInicio = microtime(true);
        
        // Reducir memory limit ya que optimizamos las queries
        ini_set('memory_limit', '1024M'); 
        
        $pedidoDetalle = [];
        $cliente = Cliente::find($pedido->cliente);
        
        // ✅ OPTIMIZACIÓN: Usar where en lugar de orWhere
        //$tiempoQuery = microtime(true);
        $detalle = Pedidodetalle::with('productos.colores')
            ->where('pedido', $pedido->id)
            ->get();
        $detalleNn = Pedidodetallenn::where('pedido', $pedido->id)->get();
        //$this->logTiempo('Transformer: Queries de detalles', $tiempoQuery);

        // ✅ OPTIMIZACIÓN: Cargar todas las imágenes en una sola query (elimina N+1)
        //$tiempoImagenes = microtime(true);
        $imagenIds = $detalle->pluck('productos.imagenPrincipal')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        
        $imagenes = collect();
        if (!empty($imagenIds)) {
            $imagenes = Fotoproducto::whereIn('id', $imagenIds)->get()->keyBy('id');
        }
        //$this->logTiempo('Transformer: Query de imágenes', $tiempoImagenes);

        $cantidadTotal = 0;
        $subtotal = 0;

        // ✅ OPTIMIZACIÓN: Descargar todas las imágenes en paralelo antes del loop
        //$tiempoDescarga = microtime(true);
        $urlsImagenes = [];
        foreach ($detalle as $d) {
            $imagenPrincipal = null;
            if ($d->productos && $d->productos->imagenPrincipal) {
                $imagenPrincipal = $imagenes[$d->productos->imagenPrincipal] ?? null;
            }

            if ($imagenPrincipal && isset($imagenPrincipal->url)) {
                $urlsImagenes[] = env('URL_IMAGENES_PRODUCTOS') . $imagenPrincipal->nombre;
            } elseif ($d->productos && $d->productos->imagenPrincipal) {
                $urlsImagenes[] = env('URL_IMAGENES_PRODUCTOS') . $d->productos->imagenPrincipal . '.jpg';
            } else {
                $urlsImagenes[] = env('URL_IMAGENES_PRODUCTOS') . '0.jpg';
            }
        }
        
        // Descargar todas las imágenes en paralelo usando multi-curl
        $imagenesBase64 = $this->descargarImagenesEnParalelo($urlsImagenes);
        //$this->logTiempo('Transformer: Descarga de imágenes en paralelo', $tiempoDescarga);

        $tiempoLoop = microtime(true);
        $indiceImagen = 0;
        foreach ($detalle as $d) {
            // Usar la imagen ya descargada
            $imagenBase64 = $imagenesBase64[$indiceImagen] ?? '';
            $indiceImagen++;

            $precio = $d->precio * $d->cantidad;
            $cantidadTotal += $d->cantidad;
            $subtotal += $d->precio * $d->cantidad;
            $pedidoDetalle[] = [
                'cantidad' => $d->cantidad,
                'codigo' => optional($d->productos)->codigo ?? '',
                'nombreProducto' => optional($d->productos)->nombre ?? '',
                'producto' => optional($d->productos)->id ?? 0,
                'nombreColor' => optional($d->productos)->color ?? '',
                'color' => optional($d->productos->colores)->id ?? '',
                'precio' => number_format($d->precio, 2),
                'total' => number_format($precio, 2),
                'imagen' => $imagenBase64, // Usar base64 en lugar de URL
                'imagenPrincipal' => optional($d->productos)->imagenPrincipal,
            ];
        }
        //$this->logTiempo('Transformer: Loop de procesamiento', $tiempoLoop);

        $tiempoSort = microtime(true);
        usort($pedidoDetalle, function ($a, $b) {
            return strcmp($a['nombreProducto'], $b['nombreProducto']);
        });
        //$this->logTiempo('Transformer: Ordenamiento', $tiempoSort);

        $tiempoDetalleNn = microtime(true);
        $imagenDefault = env('URL_IMAGENES_PRODUCTOS') . '0.jpg';
        $imagenDefaultBase64 = $this->convertirImagenABase64($imagenDefault);
        
        foreach ($detalleNn as $dNn) {
            $cantidadTotal += $dNn->cantidad;
            $subtotal += $dNn->precio * $dNn->cantidad;

            $pedidoDetalle[] = [
                'cantidad' => $dNn->cantidad,
                'codigo' => $dNn->id,
                'nombreProducto' => $dNn->descripcion,
                'producto' => 0,
                'nombreColor' => '',
                'color' => '',
                'precio' => number_format($dNn->precio, 2),
                'total' => number_format($dNn->precio * $dNn->cantidad, 2),
                'imagen' => $imagenDefaultBase64, // Reutilizar la misma imagen
                'imagenPrincipal' => 0,
            ];
        }
        //$this->logTiempo('Transformer: Procesamiento detalle NN', $tiempoDetalleNn);

        $descuentoPorcentual = $subtotal * ($pedido->DescuentoPorcentual / 100);
        $totalDescuentos = $descuentoPorcentual + $pedido->DescuentoPromociones + $pedido->DescuentoNeto;
        $totalEnvio = $pedido->TotalEnvio;

        return [
            'tienda' => [
                'direccion' => 'MDO INC 2618 NW 112th AVENUE. MIAMI, FL 33172',
                'telefono' => '513 9177 / 305 424 8199',
                'numero_pedido' => $pedido->id,
                'fecha_pedido' => DateHelper::ToDateCustom($pedido->fecha),
                'email' => 'ventas@mayoristasdeopticas.com',
            ],
            'cliente' => [
                'nombre' => optional($cliente)->nombre ?? '',
                'numero' => optional($cliente)->id ?? '',
                'telefono' => optional($cliente)->telefono ?? '',
                'direccion' => optional($cliente)->direccion ?? '',
                'email' => optional($cliente)->email ?? '',
            ],
            'detalle' => $pedidoDetalle,
            'pedido' => [
                'subTotal' => number_format($subtotal, 2),
                'descuentoPorcentual' => number_format($pedido->DescuentoPorcentual, 2),
                'descuentoPorcentualTotal' => number_format($descuentoPorcentual, 2),
                'descuentoPromociones' => number_format($pedido->DescuentoPromociones, 2),
                'descuentoNeto' => number_format($pedido->DescuentoNeto, 2),
                'total' => number_format($pedido->total - $pedido->TotalEnvio, 2),
                'totalEnvio' => number_format($totalEnvio, 2),
                'subTotalConEnvio' => number_format($pedido->total, 2),
                'creditoDisponible' => number_format($cliente->ctacte ?? 0, 2),
                'totalAabonar' => number_format($pedido->total - ($cliente->ctacte ?? 0), 2),
                'cantidad' => $cantidadTotal,
            ],
        ];
        
        //$this->logTiempo('Transformer: TOTAL transformación', $tiempoInicio);
    }

    /**
     * Descarga múltiples imágenes en paralelo usando multi-curl
     * 
     * @param array $urls Array de URLs de imágenes
     * @return array Array de imágenes en formato base64
     */
    private function descargarImagenesEnParalelo($urls)
    {
        if (empty($urls)) {
            return [];
        }

        $multiHandle = curl_multi_init();
        $curlHandles = [];
        $resultados = [];

        // Crear un handle curl para cada URL
        foreach ($urls as $index => $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout de 3 segundos por imagen
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            
            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$index] = $ch;
            $resultados[$index] = ''; // Inicializar con string vacío
        }

        // Ejecutar todas las descargas en paralelo
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle, 0.1);
        } while ($running > 0);

        // Procesar resultados
        foreach ($curlHandles as $index => $ch) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                $imageData = curl_multi_getcontent($ch);
                if ($imageData !== false && !empty($imageData)) {
                    // Obtener tipo MIME
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_buffer($finfo, $imageData);
                    finfo_close($finfo);

                    if (!$mimeType || !in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                        $mimeType = 'image/jpeg';
                    }

                    $imagenReducida = $this->reducirImagen($imageData, 300, 70);

                    if ($imagenReducida) {
                        $base64 = base64_encode($imagenReducida);
                        $resultados[$index] = 'data:image/jpeg;base64,' . $base64;
                    }
                }
            }
            
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $resultados;
    }

    /**
     * Verifica si el logging de tiempos está habilitado
     * 
     * Se puede activar/desactivar mediante la variable de entorno ENABLE_PAGO_WEB_TIME_LOGS
     * o mediante config('pago_web.enable_time_logs', false)
     * 
     * @return bool true si los logs están habilitados, false en caso contrario
     */
    private function isTimeLoggingEnabled()
    {
        // Prioridad: 1) Variable de entorno, 2) Config, 3) Default false
        $envValue = env('ENABLE_PAGO_WEB_TIME_LOGS', null);
        if ($envValue !== null) {
            return filter_var($envValue, FILTER_VALIDATE_BOOLEAN);
        }
        
        return config('pago_web.enable_time_logs', false);
    }

    /**
     * Registra tiempo de una operación en el log
     * 
     * NOTA: Este logging se puede activar/desactivar mediante:
     * - Variable de entorno: ENABLE_PAGO_WEB_TIME_LOGS=true
     * - Configuración: config('pago_web.enable_time_logs', true)
     * 
     * Por defecto está DESACTIVADO para no impactar el rendimiento en producción.
     * Activar solo cuando se necesite analizar tiempos de ejecución.
     */
    private function logTiempo($operacion, $tiempoInicio)
    {
        // Si el logging está deshabilitado, salir inmediatamente
        if (!$this->isTimeLoggingEnabled()) {
            return;
        }
        
        $duracion = (microtime(true) - $tiempoInicio) * 1000; // en ms
        $fecha = date('Y-m-d');
        $logFile = storage_path('logs/pago_web_tiempos_' . $fecha . '.txt');
        $timestamp = date('H:i:s');
        
        $linea = sprintf(
            "[%s] %s | Duración: %.2fms\n",
            $timestamp,
            $operacion,
            $duracion
        );
        
        file_put_contents($logFile, $linea, FILE_APPEND | LOCK_EX);
    }

    /**
     * Convierte una imagen desde URL a Base64 para evitar descargas durante la generación del PDF
     * 
     * @param string $url URL de la imagen
     * @return string Imagen en formato base64 o string vacío si falla
     */
    private function convertirImagenABase64($url)
    {
        if (empty($url)) {
            return '';
        }

        // Si ya es base64, retornarlo
        if (strpos($url, 'data:image') === 0) {
            return $url;
        }

        try {
            // Configurar contexto para descarga con timeout
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5, // Timeout de 5 segundos
                    'user_agent' => 'Mozilla/5.0',
                ]
            ]);

            // Intentar descargar la imagen
            $imageData = @file_get_contents($url, false, $context);
            
            if ($imageData === false) {
                // Si falla, retornar string vacío (el PDF mostrará imagen rota pero no bloqueará)
                return '';
            }

            // Obtener tipo MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            // Si no se puede determinar el tipo, usar jpeg por defecto
            if (!$mimeType || !in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                $mimeType = 'image/jpeg';
            }

            // Convertir a base64
            $base64 = base64_encode($imageData);
            return 'data:' . $mimeType . ';base64,' . $base64;

        } catch (\Exception $e) {
            // En caso de error, retornar string vacío
            Log::warning('FindByIdTransformer::convertirImagenABase64 - Error al convertir imagen', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }
}
