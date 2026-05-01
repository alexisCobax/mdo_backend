<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Recibo;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Producto;
use App\Helpers\LogHelper;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use App\Models\Pedidodetalle;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Models\Cupondescuento;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\CalcEnvioHelper;
use App\Helpers\CalcTotalHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Mail\EnvioMailAgradecimientoCompra;
use App\Transformers\Pdf\FindByIdTransformer;
use App\Services\ProformaService;
use App\Jobs\GenerarProformaJob;
use App\Jobs\EnviarWebhookGHLJob;
use App\Services\CarritoWebService;

class PagoWebService
{
    private static $tiempoInicio = null;
    private static $tiempoAnterior = null;

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
     * Registra un evento con timestamp y duración en archivo de texto
     *
     * NOTA: Este logging se puede activar/desactivar mediante:
     * - Variable de entorno: ENABLE_PAGO_WEB_TIME_LOGS=true
     * - Configuración: config('pago_web.enable_time_logs', true)
     *
     * Por defecto está DESACTIVADO para no impactar el rendimiento en producción.
     * Activar solo cuando se necesite analizar tiempos de ejecución.
     *
     * @param string $evento Nombre del evento
     * @param int|null $clienteId ID del cliente (opcional)
     * @param int|null $carritoId ID del carrito (opcional)
     */
    private function registrarTiempo($evento, $clienteId = null, $carritoId = null)
    {
        // Si el logging está deshabilitado, salir inmediatamente
        if (!$this->isTimeLoggingEnabled()) {
            return;
        }
        $tiempoActual = microtime(true);

        // Inicializar tiempo de inicio si es la primera llamada
        if (self::$tiempoInicio === null) {
            self::$tiempoInicio = $tiempoActual;
            self::$tiempoAnterior = $tiempoActual;
        }

        // Calcular duraciones
        $duracionDesdeInicio = $tiempoActual - self::$tiempoInicio;
        $duracionDesdeAnterior = $tiempoActual - self::$tiempoAnterior;

        // Actualizar tiempo anterior
        self::$tiempoAnterior = $tiempoActual;

        $timestamp = date('H:i:s');
        $fecha = date('Y-m-d');
        $logFile = storage_path('logs/pago_web_tiempos_' . $fecha . '.txt');

        // Formatear duraciones
        $duracionTotal = $this->formatearDuracion($duracionDesdeInicio);
        $duracionPaso = $this->formatearDuracion($duracionDesdeAnterior);

        $linea = sprintf(
            "[%s] %s | Duración desde inicio: %s | Duración del paso: %s",
            $timestamp,
            $evento,
            $duracionTotal,
            $duracionPaso
        );

        if ($clienteId !== null) {
            $linea .= " | Cliente: {$clienteId}";
        }
        if ($carritoId !== null) {
            $linea .= " | Carrito: {$carritoId}";
        }

        $linea .= PHP_EOL;

        file_put_contents($logFile, $linea, FILE_APPEND | LOCK_EX);
    }

    /**
     * Formatea la duración en segundos a formato legible
     *
     * @param float $segundos Duración en segundos
     * @return string Duración formateada
     */
    private function formatearDuracion($segundos)
    {
        if ($segundos < 1) {
            return number_format($segundos * 1000, 2) . 'ms';
        } elseif ($segundos < 60) {
            return number_format($segundos, 3) . 's';
        } else {
            $minutos = floor($segundos / 60);
            $segundosRestantes = $segundos % 60;
            return sprintf('%dm %.3fs', $minutos, $segundosRestantes);
        }
    }

    public function create(Request $request)
    {
        // Resetear contadores de tiempo para este proceso
        self::$tiempoInicio = null;
        self::$tiempoAnterior = null;

        $this->registrarTiempo('=== INICIO PROCESO DE PAGO ===');

        $carrito = CarritoHelper::getCarrito();
        $this->registrarTiempo('1. Obtener Carrito', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $productosCarrito = Carritodetalle::where('carrito', $carrito['id'])->get();
        $this->registrarTiempo('2. Obtener Productos del Carrito', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $totalPorProducto = $productosCarrito->map(function ($item) {
            return $item->precio * $item->cantidad;
        });

        $subtotal = $totalPorProducto->sum();
        $this->registrarTiempo('3. Calcular Subtotal', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $cantidades = $productosCarrito->pluck('cantidad');
        $cantidad = $cantidades->sum();
        $this->registrarTiempo('4. Calcular Cantidad Total', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $cupon = Cupondescuento::where('id', $carrito['cupon'])->first();
        $this->registrarTiempo('5. Obtener Cupón de Descuento', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $descuentos = '0.00';

        if ($cupon) {
            $descuentos = $subtotal * $cupon->descuentoPorcentual / 100;
            $this->registrarTiempo('5.1. Aplicar Descuento del Cupón', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        }

        $calculosGenerales = CalcTotalHelper::calcular($subtotal, $cantidad, $descuentos);
        $total = number_format($calculosGenerales['totalConEnvio'], 2, '', '');
        $this->registrarTiempo('6. Calcular Totales (con envío)', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $nombreCliente = $carrito['clienteNombre'] ?? '';

        // Validar que el token esté presente y tenga el formato correcto
        $token = $request->input('token');

        // Log detallado del request para debugging
        Log::info('PagoWebService::create - Request recibido', [
            'has_token' => !empty($token),
            'token_type' => gettype($token),
            'token_length' => $token ? strlen($token) : 0,
            'token_preview' => $token ? substr($token, 0, 30) . '...' : 'null',
            'request_keys' => array_keys($request->all()),
            'request_all' => $request->all() // Temporal para debugging
        ]);

        if (empty($token)) {
            Log::error('PagoWebService::create - Token faltante', [
                'request_data' => $request->all(),
                'request_json' => $request->getContent()
            ]);
            return response()->json([
                'error' => [
                    'message' => 'Token de tarjeta no proporcionado',
                    'code' => 'missing_token'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        // Limpiar el token (eliminar espacios)
        $token = trim($token);

        // Validar formato del token (debe comenzar con "clv_")
        if (!preg_match('/^clv_/', $token)) {
            Log::error('PagoWebService::create - Token con formato inválido', [
                'token_preview' => substr($token, 0, 50),
                'token_length' => strlen($token),
                'token_first_chars' => substr($token, 0, 10),
                'full_token' => $token // Temporal para debugging
            ]);
            return response()->json([
                'error' => [
                    'message' => 'Token de tarjeta inválido. Debe comenzar con "clv_"',
                    'code' => 'invalid_token_format',
                    'token_received' => substr($token, 0, 20) . '...' // Temporal para debugging
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        // ✅ VERIFICACIÓN DE STOCK: Recalcular el carrito antes de procesar el pago
        // Si hay cambios, se recalcula automáticamente y se continúa con el pago usando los nuevos totales
        $this->registrarTiempo('6.5. Verificar Stock antes de Pago', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        $carritoWebService = new CarritoWebService();
        $recalculoInfo = $carritoWebService->recalcularCarritoPorStock($carrito['id']);

        // Guardar el total anterior para el log
        $totalAnterior = $total;

        // Si hubo cambios en el stock, recalcular los totales con los nuevos datos
        if ($recalculoInfo['productos_actualizados'] > 0 || $recalculoInfo['productos_eliminados'] > 0) {
            Log::info('PagoWebService::create - Stock recalculado antes del pago', [
                'carrito_id' => $carrito['id'],
                'productos_actualizados' => $recalculoInfo['productos_actualizados'],
                'productos_eliminados' => $recalculoInfo['productos_eliminados'],
                'total_anterior' => $totalAnterior
            ]);

            // Recalcular los totales con los nuevos datos del carrito
            $productosCarritoActualizados = Carritodetalle::where('carrito', $carrito['id'])->get();

            if ($productosCarritoActualizados->isEmpty()) {
                // Si no quedan productos, el carrito está vacío - esto sí debe detenerse
                return response()->json([
                    'error' => [
                        'message' => 'Su carrito está vacío. Todos los productos fueron eliminados por falta de stock.',
                        'code' => 'stock_recalculation_empty_cart',
                        'recalculoStock' => [
                            'huboCambios' => true,
                            'productos_actualizados' => $recalculoInfo['productos_actualizados'],
                            'productos_eliminados' => $recalculoInfo['productos_eliminados']
                        ]
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }

            // Recalcular subtotal, cantidad y totales con los datos actualizados
            $totalPorProductoActualizado = $productosCarritoActualizados->map(function ($item) {
                return $item->precio * $item->cantidad;
            });

            $subtotal = $totalPorProductoActualizado->sum();
            $cantidadesActualizadas = $productosCarritoActualizados->pluck('cantidad');
            $cantidad = $cantidadesActualizadas->sum();

            // Recalcular descuentos si hay cupón
            if ($cupon) {
                $descuentos = $subtotal * $cupon->descuentoPorcentual / 100;
            }

            $calculosGenerales = CalcTotalHelper::calcular($subtotal, $cantidad, $descuentos);
            $total = number_format($calculosGenerales['totalConEnvio'], 2, '', '');

            Log::info('PagoWebService::create - Totales recalculados después de verificación de stock', [
                'carrito_id' => $carrito['id'],
                'total_anterior' => $totalAnterior,
                'total_actualizado' => $total,
                'subtotal_actualizado' => $subtotal
            ]);
        }

        $this->registrarTiempo('7. Iniciar Procesamiento de Pago con Clover', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        $pago = $this->creditCard($total, $token, $nombreCliente);
        $this->registrarTiempo('7.1. Respuesta de Clover recibida', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

        $pagoResponse = $pago->getContent();

        $pago = json_decode($pagoResponse);
        $this->registrarTiempo('8. Decodificar Respuesta de Clover', $carrito['cliente'] ?? null, $carrito['id'] ?? null);


        /* Guardo Transaccion**/
        $transaccion = null;
        try {
            Log::info('PagoWebService::create - Intentando guardar transacción', [
                'cliente' => $carrito['cliente'],
                'pago_response_preview' => substr($pagoResponse, 0, 200)
            ]);

            $this->registrarTiempo('9. Iniciar Guardado de Transacción', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
            $transaccion = $this->saveTransaction($carrito['cliente'], json_encode([]), $pago, $pagoResponse);
            $this->registrarTiempo('9.1. Transacción Guardada', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

            if ($transaccion && $transaccion->id) {
                Log::info('PagoWebService::create - Transacción guardada exitosamente en create', [
                    'transaccion_id' => $transaccion->id,
                    'cliente' => $carrito['cliente']
                ]);
            } else {
                Log::warning('PagoWebService::create - saveTransaction retornó null o sin ID', [
                    'cliente' => $carrito['cliente'],
                    'transaccion' => $transaccion
                ]);
            }
        } catch (\Exception $e) {
            // Si falla guardar la transacción, logueamos el error pero continuamos
            // Es importante registrar todos los intentos de pago, incluso los fallidos
            Log::error('PagoWebService::create - Error crítico al guardar transacción (continuando)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cliente' => $carrito['cliente'],
                'pago_response' => $pagoResponse
            ]);
        }

        /* Si concreto la operacion realizo el guardado de datos **/
        $this->registrarTiempo('10. Validar Aprobación del Pago', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        if ((isset($pago->paid) && $pago->paid)) {

        try {
            $pedido = null;
            $recibo = null;

            // ✅ OPTIMIZACIÓN: Usar transacción de BD para garantizar consistencia
            $this->registrarTiempo('11. Iniciar Transacción de Base de Datos', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
            $service = $this; // Variable local para usar dentro de la closure
            DB::transaction(function() use ($calculosGenerales, $carrito, $pago, $productosCarrito, $transaccion, $subtotal, $cantidad, $descuentos, $total, &$pedido, &$recibo, $service) {
                /** Guardo pedido**/
                // Crear un objeto request simulado para mantener compatibilidad con savePedido
                $requestSimulado = new \stdClass();
                $requestSimulado->metodopago = 'tarjeta';
                $service->registrarTiempo('11.1. Crear Pedido', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
                $pedido = $service->savePedido($calculosGenerales, $carrito['cliente'], $requestSimulado);
                $service->registrarTiempo('11.1.1. Pedido Creado', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

                if (!$pedido) {
                    Log::error('PagoWebService::create - Error al crear pedido', [
                        'cliente' => $carrito['cliente'],
                        'carrito' => $carrito['id'],
                        'total' => $total
                    ]);
                    throw new \Exception('Error al crear el pedido');
                }

                /** genero recibo**/
                $service->registrarTiempo('11.2. Crear Recibo', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
                $recibo = [
                    'cliente' => $carrito['cliente'],
                    'formaDePago' => 2,
                    'total' => isset($pago->amount) ? $pago->amount / 100 : 0,
                    'observaciones' => 'Pago realizado a traves de la plataforma de clover',
                    'pedido' => $pedido->id,
                    'garantia' => 0,
                    'anulado' => 0,
                    'fecha' => NOW(),
                ];

                $recibo = Recibo::create($recibo);
                $service->registrarTiempo('11.2.1. Recibo Creado', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

                if (!$recibo) {
                    Log::error('PagoWebService::create - Error al crear recibo', [
                        'cliente' => $carrito['cliente'],
                        'pedido' => $pedido->id,
                        'carrito' => $carrito['id']
                    ]);
                    throw new \Exception('Error al crear el recibo');
                }

                /* ✅ OPTIMIZACIÓN: Guardar detalle de pedidos con bulk operations */
                $service->registrarTiempo('11.3. Guardar Detalles de Pedido y Actualizar Stock', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
                $service->saveDetallePedidoOptimizado($productosCarrito, $pedido);
                $service->registrarTiempo('11.3.1. Detalles Guardados y Stock Actualizado', $carrito['cliente'] ?? null, $carrito['id'] ?? null);

                /* Elimino carrito **/
                $service->registrarTiempo('11.4. Actualizar Estado del Carrito', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
                $carritoUpdate = Carrito::find($carrito['id']);
                if ($carritoUpdate) {
                    $carritoUpdate->estado = 1;
                    $carritoUpdate->save();
                }

                /* Actualizo la transaccion con los datos del pedido, carrito y recibo **/
                $service->registrarTiempo('11.5. Actualizar Transacción con Datos Completos', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
                $service->updateTransaction($transaccion, $pedido, $carrito['id'], $recibo->id, $calculosGenerales, $productosCarrito, $subtotal, $cantidad, $descuentos, $total);
                $service->registrarTiempo('11.6. Transacción de BD Completada', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
            });

            // Verificar que el pedido se creó correctamente
            if (!$pedido || !$recibo) {
                throw new \Exception('Error: Pedido o recibo no se crearon correctamente');
            }

            // ✅ OPTIMIZACIÓN: Obtener cliente fuera de la transacción
            $this->registrarTiempo('12. Obtener Datos del Cliente', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
            $cliente = Cliente::where('id', $carrito['cliente'])->first();
            $pedidoId = $pedido->id;

            // ✅ OPTIMIZACIÓN: Ejecutar tareas pesadas después de responder
            // Si hay queue configurado, se ejecutan en background
            // Si no hay queue (modo sync), se ejecutan después de la respuesta usando register_shutdown_function
            $this->registrarTiempo('13. Programar Tareas Asíncronas (PDF, Email, Webhook)', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
            $this->ejecutarTareasAsincronas($pedidoId, $carrito['cliente'], $cantidad, $subtotal, $descuentos, $total, $cliente->direccionShape ?? '');

            Log::info('PagoWebService::create - Pedido creado exitosamente', [
                'pedido_id' => $pedidoId,
                'cliente' => $carrito['cliente']
            ]);

        } catch (\Exception $e) {
            $this->registrarTiempo('ERROR: ' . $e->getMessage(), $carrito['cliente'] ?? null, $carrito['id'] ?? null);
            Log::error('PagoWebService::create - Error en el proceso de creación de pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cliente' => $carrito['cliente'],
                'carrito' => $carrito['id'],
                'transaccion_id' => $transaccion->id ?? null
            ]);

            return response()->json([
                'error' => 'Error al procesar el pedido',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // ✅ OPTIMIZACIÓN: Respuesta inmediata (sin esperar PDF ni webhook)
        $this->registrarTiempo('14. Retornar Respuesta Exitosa', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        $this->registrarTiempo('=== FIN PROCESO DE PAGO (ÉXITO) ===', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        return response()->json([
            'status' => 200,
            'mensaje' => 'El pedido fue generado de forma exitosa',
            'pedidoId' => $pedido->id
        ], Response::HTTP_OK);
        }

        $this->registrarTiempo('=== FIN PROCESO DE PAGO (RECHAZADO) ===', $carrito['cliente'] ?? null, $carrito['id'] ?? null);
        return response()->json(['error' => $pago], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function createNotCreditCard($request)
    {
        $carrito = CarritoHelper::getCarrito();

        $productosCarrito = Carritodetalle::where('carrito', $carrito['id'])->get();

        $totalPorProducto = $productosCarrito->map(function ($item) {
            return $item->precio * $item->cantidad;
        });

        $subtotal = $totalPorProducto->sum();

        $cantidades = $productosCarrito->pluck('cantidad');
        $cantidad = $cantidades->sum();

        $cupon = Cupondescuento::where('id', $carrito['cupon'])->first();

        $descuentos = '0.00';

        if ($cupon) {
            $descuentos = $subtotal * $cupon->descuentoPorcentual / 100;
        }

        $calculosGenerales = CalcTotalHelper::calcular($subtotal, $cantidad, $descuentos);
        $total = number_format($calculosGenerales['totalConEnvio'], 2, '', '');
        $nombreCliente = $carrito['clienteNombre'] ?? '';
        // $pago = $this->creditCard($total, $request->token, $nombreCliente);

        // $pagoResponse = $pago->getContent();

        // $pago = json_decode($pagoResponse);


        /* Guardo Transaccion**/
        // $this->saveTransaction($carrito['cliente'], json_encode([]), $pago, $pagoResponse);

        /* Si concreto la operacion realizo el guardado de datos **/
        // if (isset($pago->paid) && $pago->paid) {

        /** Guardo pedido**/
        $pedido = $this->savePedido($calculosGenerales, $carrito['cliente'], $request);
        /** genero recibo**/
        // $recibo = [
        //     'cliente' => $carrito['cliente'],
        //     'formaDePago' => 2,
        //     'total' => $pago->amount / 100,
        //     'observaciones' => 'Pago realizado a traves de la plataforma de clover',
        //     'pedido' => $pedido->id,
        //     'garantia' => 0,
        //     'anulado' => 0,
        //     'fecha' => NOW(),
        // ];

        // $recibo = Recibo::create($recibo);

        /* Guardo detalle de pedidos **/
        $this->saveDetallePedido($productosCarrito, $pedido);

        /* Elimino carrito **/
        $carritoUpdate = Carrito::find($carrito['id']);
        if ($carritoUpdate) {
            $carritoUpdate->estado = 1;
            $carritoUpdate->save();
        } else {
            Log::warning('PagoWebService::createNotCreditCard - No se encontró el carrito con ID: ' . ($carrito['id'] ?? 'NULL'));
        }

        // if (!$recibo) {
        //     return response()->json(['error' => 'Failed to create Recibo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }


        // /** ENVIO EMAIL **/
        // $cliente = Cliente::where('id', $carrito['cliente'])->first();


        // /** PARA EMAIL **/
        // $pedidoId = $pedido->id;
        // $datosParaEmail = [
        //     "pedidoNumero" => $pedidoId,
        //     "totalArticulos" => $cantidad,
        //     "subtotal" => $subtotal,
        //     "costoEnvio" => CalcEnvioHelper::calcular($cantidad),
        //     "descuentos" => isset($descuentos) && !empty($descuentos) ? $descuentos : '',
        //     "total" => $total,
        //     "fecha" => date('Y-m-d'),
        //     "direccionEnvio" => $cliente->direccionShape,
        //     "metodoPago" => 'Tarjeta de crédito'

        // ];

        // $proformaService = new ProformaService;
        // $proformaService->proformaParaEmail($pedidoId);

        // Log::info($datosParaEmail);

        // /** Envio por email PDF**/
        // $cuerpo = '';
        // $emailMdo = env('MAIL_COTIZACION_MDO');
        // if ($cliente->email) {

        //     $destinatarios = [
        //         $emailMdo,
        //         $cliente->email,
        //         'doralice@mayoristasdeopticas.com'
        //     ];
        // } else {
        //     $destinatarios = [
        //         $emailMdo,
        //         'doralice@mayoristasdeopticas.com'
        //     ];
        // }

        // $rutaArchivoZip = storage_path('app/public/tmpdf/' . 'proforma_'.$pedidoId.'.pdf');

        // // $rutaArchivoFijo = storage_path('app/public/fijos/Inf.TRANSFERENCIA_BANCARIA.pdf');

        // Mail::to($destinatarios)->send(new EnvioMailAgradecimientoCompra($cuerpo, $rutaArchivoZip, $datosParaEmail));


        // /**********************************/

        // $ghl = new GoHighLevelService();
        // $ghl->carritoEstado([$cliente->email]);

        return response()->json(['status' => 200, 'mensaje' => 'El pedido fue generado de forma exitosa', 'pedidoId' => $pedido->id], Response::HTTP_OK);
        // }

        // return response()->json(['error' => $pago], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function savePedido($calculosGenerales, $idCliente, $request)
    {

        $cliente = Cliente::where('id', $idCliente)->first();
        $metodopago = 0;
        switch($request->metodopago){
            case 'tarjeta':
                $metodopago = 2;
                break;
                case 'transferencia':
                    $metodopago = 4;
                    break;
                    case 'wise':
                        $metodopago = 10;
                        break;
                        case 'zelle':
                            $metodopago = 9;
                            break;
                            case 'agente':
                                $metodopago = 11;
                                break;
        }

        $pedido = new Pedido;
        $pedido->fecha = NOW();
        $pedido->cliente = $idCliente;
        $pedido->estado = 1;
        $pedido->vendedor = 1;
        $pedido->formaDePago = $metodopago;
        $pedido->invoice = 0;
        $pedido->total = '0.00';
        $pedido->descuentoPorcentual = '0.00';
        $pedido->descuentoNeto = $calculosGenerales['descuentos'];
        $pedido->totalEnvio = $calculosGenerales['totalEnvio'];
        $pedido->origen = 1;

        $pedido->nombreEnvio = $cliente->nombreEnvio;
        $pedido->domicilioEnvio = $cliente->direccionShape;
        $pedido->paisEnvio = $cliente->paisShape;
        $pedido->regionEnvio = $cliente->regionEnvio;
        $pedido->ciudadEnvio = $cliente->ciudadEnvio;
        $pedido->cpEnvio = $cliente->cpShape;
        $pedido->transportadoraNombre = $cliente->transportadora;
        $pedido->telefonoTransportadora = $cliente->telefonoTransportadora;
        $pedido->observaciones = $cliente->observacione;
        $pedido->tipoDeEnvio = $cliente->tipoDeEnvio;
        $pedido->save();

        return $pedido;
    }

    public function saveTransaction($cliente, $pedido, $status, $data)
    {
        // Campos base que siempre existen - NO usar try-catch aquí para que cualquier error se propague
        // Asegurarse de que pedido sea siempre un entero válido (0 si no hay pedido)
        $pedidoValue = 0;
        if (is_numeric($pedido) && $pedido > 0) {
            $pedidoValue = (int)$pedido;
        }

        // Preparar datos básicos de la transacción (solo campos que SIEMPRE existen)
        $transaccionData = [
            'fecha' => NOW(),
            'cliente' => (int)$cliente, // Asegurar que sea entero
            'pedido' => $pedidoValue, // Siempre un entero válido, nunca null
            'resultado' => json_encode($status),
            'ctr' => is_string($data) ? $data : json_encode($data),
        ];

        // Intentar agregar campos nuevos solo si existen en la tabla
        // Si falla la verificación del schema, continuamos sin esos campos
        try {
            $schema = Schema::getColumnListing('transaccion');

            if (in_array('carrito', $schema)) {
                $transaccionData['carrito'] = 0;
            }
            if (in_array('recibo', $schema)) {
                $transaccionData['recibo'] = 0;
            }
            if (in_array('payload', $schema)) {
                $transaccionData['payload'] = null;
            }
        } catch (\Exception $schemaError) {
            // Si hay error al verificar schema, continuar sin los campos nuevos
            Log::warning('PagoWebService::saveTransaction - No se pudieron verificar columnas nuevas, continuando sin ellas', [
                'error' => $schemaError->getMessage()
            ]);
        }

        // Usar insert directo para evitar problemas con Eloquent
        // Esto asegura que los valores se inserten exactamente como los especificamos
        try {
            Log::info('PagoWebService::saveTransaction - Intentando insertar transacción', [
                'cliente' => $cliente,
                'pedido' => $pedidoValue,
                'campos' => array_keys($transaccionData)
            ]);

            $transaccionId = DB::table('transaccion')->insertGetId($transaccionData);

            if (!$transaccionId || $transaccionId <= 0) {
                throw new \Exception('No se pudo obtener el ID de la transacción insertada. ID retornado: ' . ($transaccionId ?? 'null'));
            }

            // Verificar que la transacción se guardó correctamente
            $transaccion = Transaccion::find($transaccionId);

            if (!$transaccion) {
                // Intentar obtener directamente de la BD
                $transaccionFromDb = DB::table('transaccion')->where('id', $transaccionId)->first();
                if ($transaccionFromDb) {
                    Log::warning('PagoWebService::saveTransaction - Transacción existe en BD pero no se puede obtener con Eloquent', [
                        'transaccion_id' => $transaccionId
                    ]);
                    // Crear un objeto transacción manualmente
                    $transaccion = new Transaccion();
                    $transaccion->id = $transaccionFromDb->id;
                    $transaccion->fecha = $transaccionFromDb->fecha;
                    $transaccion->cliente = $transaccionFromDb->cliente;
                    $transaccion->pedido = $transaccionFromDb->pedido;
                    $transaccion->resultado = $transaccionFromDb->resultado;
                    $transaccion->ctr = $transaccionFromDb->ctr;
                } else {
                    throw new \Exception("No se pudo encontrar la transacción con ID: {$transaccionId} ni en Eloquent ni en BD directa");
                }
            }

            Log::info('PagoWebService::saveTransaction - Transacción guardada exitosamente', [
                'transaccion_id' => $transaccionId,
                'cliente' => $cliente,
                'pedido' => $pedidoValue,
                'verificado_en_bd' => $transaccion ? 'yes' : 'no'
            ]);

            return $transaccion;
        } catch (\Exception $e) {
            Log::error('PagoWebService::saveTransaction - Error al guardar transacción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cliente' => $cliente,
                'pedido_value' => $pedidoValue,
                'transaccion_data' => $transaccionData,
                'sql_error' => $e->getCode()
            ]);
            throw $e;
        }
    }

    public function updateTransaction($transaccion, $pedido, $carritoId, $reciboId, $calculosGenerales, $productosCarrito, $subtotal, $cantidad, $descuentos, $total)
    {
        try {
            // Preparar datos del pedido para el payload
            $payloadData = [
                'pedido' => [
                    'id' => $pedido->id,
                    'fecha' => $pedido->fecha,
                    'cliente' => $pedido->cliente,
                    'estado' => $pedido->estado,
                    'vendedor' => $pedido->vendedor,
                    'formaDePago' => $pedido->formaDePago,
                    'total' => $pedido->total,
                    'descuentoNeto' => $pedido->descuentoNeto,
                    'totalEnvio' => $pedido->totalEnvio,
                    'origen' => $pedido->origen,
                    'nombreEnvio' => $pedido->nombreEnvio,
                    'domicilioEnvio' => $pedido->domicilioEnvio,
                    'paisEnvio' => $pedido->paisEnvio,
                    'regionEnvio' => $pedido->regionEnvio,
                    'ciudadEnvio' => $pedido->ciudadEnvio,
                    'cpEnvio' => $pedido->cpEnvio,
                    'transportadoraNombre' => $pedido->transportadoraNombre,
                    'telefonoTransportadora' => $pedido->telefonoTransportadora,
                    'tipoDeEnvio' => $pedido->tipoDeEnvio,
                ],
                'calculos' => [
                    'subtotal' => $subtotal,
                    'cantidad' => $cantidad,
                    'descuentos' => $descuentos,
                    'total' => $total,
                    'totalEnvio' => $calculosGenerales['totalEnvio'],
                    'totalConEnvio' => $calculosGenerales['totalConEnvio'],
                ],
                'productos' => $productosCarrito->map(function ($item) {
                    return [
                        'producto' => $item->producto,
                        'cantidad' => $item->cantidad,
                        'precio' => $item->precio,
                        'subtotal' => $item->precio * $item->cantidad,
                    ];
                })->toArray(),
                'carrito' => [
                    'id' => $carritoId,
                ],
                'recibo' => [
                    'id' => $reciboId,
                ],
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ];

            // Actualizar la transacción
            $updateData = [
                'pedido' => $pedido->id,
            ];

            // Verificar si las columnas existen antes de actualizarlas
            try {
                $schema = Schema::getColumnListing('transaccion');

                if (in_array('carrito', $schema)) {
                    $updateData['carrito'] = $carritoId;
                }
                if (in_array('recibo', $schema)) {
                    $updateData['recibo'] = $reciboId;
                }
                if (in_array('payload', $schema)) {
                    $updateData['payload'] = $payloadData;
                }
            } catch (\Exception $schemaError) {
                Log::warning('PagoWebService::updateTransaction - No se pudieron verificar columnas nuevas', [
                    'error' => $schemaError->getMessage()
                ]);
            }

            $transaccion->update($updateData);

            Log::info('PagoWebService::updateTransaction - Transacción actualizada exitosamente', [
                'transaccion_id' => $transaccion->id,
                'pedido_id' => $pedido->id,
                'carrito_id' => $carritoId,
                'recibo_id' => $reciboId
            ]);

        } catch (\Exception $e) {
            Log::error('PagoWebService::updateTransaction - Error al actualizar transacción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaccion_id' => $transaccion->id ?? null,
                'pedido_id' => $pedido->id ?? null,
                'carrito_id' => $carritoId ?? null,
                'recibo_id' => $reciboId ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Método optimizado para guardar detalles de pedido
     * Elimina problema N+1 y usa bulk operations
     */
    public function saveDetallePedidoOptimizado($productosCarrito, $pedido)
    {
        // ✅ OPTIMIZACIÓN: Cargar todos los productos en una sola query (evita N+1)
        $productoIds = $productosCarrito->pluck('producto')->unique()->toArray();
        $productos = Producto::whereIn('id', $productoIds)->get()->keyBy('id');

        $detalles = [];
        $totalPedido = 0;
        $updatesStock = [];

        foreach ($productosCarrito as $pc) {
            $producto = $productos[$pc->producto] ?? null;

            if (!$producto) {
                Log::warning('PagoWebService::saveDetallePedidoOptimizado - Producto no encontrado', [
                    'producto_id' => $pc->producto,
                    'pedido_id' => $pedido->id
                ]);
                continue;
            }

            // Calcular cantidad disponible
            $cantidad = min($pc->cantidad, $producto->stock);

            if ($cantidad <= 0) {
                Log::warning('PagoWebService::saveDetallePedidoOptimizado - Stock insuficiente', [
                    'producto_id' => $pc->producto,
                    'stock_disponible' => $producto->stock,
                    'cantidad_solicitada' => $pc->cantidad
                ]);
                continue;
            }

            $totalPedido += $pc->precio * $cantidad;

            // Preparar detalle para bulk insert
            $detalles[] = [
                'pedido' => $pedido->id,
                'producto' => $pc->producto,
                'precio' => $pc->precio,
                'cantidad' => $cantidad,
                'costo' => '0.00',
                'envio' => '0.00',
                'tax' => '0.00',
                'taxEnvio' => '0.00',
                'created_at' => NOW(),
                'updated_at' => NOW(),
            ];

            // Preparar actualización de stock
            $updatesStock[$pc->producto] = ($updatesStock[$pc->producto] ?? 0) + $cantidad;
        }

        // ✅ OPTIMIZACIÓN: Bulk insert de todos los detalles
        if (!empty($detalles)) {
            Pedidodetalle::insert($detalles);
        }

        // ✅ OPTIMIZACIÓN: Actualizar stock con decrement (más eficiente)
        foreach ($updatesStock as $productoId => $cantidadDescuento) {
            Producto::where('id', $productoId)->decrement('stock', $cantidadDescuento);
        }

        // Actualizar total del pedido
        $pedido->total = $totalPedido;
        $pedido->save();

        Log::info('PagoWebService::saveDetallePedidoOptimizado - Detalles guardados', [
            'pedido_id' => $pedido->id,
            'total_detalles' => count($detalles),
            'total_pedido' => $totalPedido
        ]);
    }

    /**
     * Método original mantenido para compatibilidad
     * @deprecated Usar saveDetallePedidoOptimizado en su lugar
     */
    public function saveDetallePedido($productosCarrito, $pedido)
    {
        // Usar método optimizado
        return $this->saveDetallePedidoOptimizado($productosCarrito, $pedido);
    }

    public function descuentoDeStock($producto, $cantidadDescuento)
    {
        $producto = Producto::findOrFail($producto);
        $stock = $producto->stock - $cantidadDescuento;
        $producto->stock = $stock;
        $producto->save();
    }

    public function sendRecibo($pedido)
    {
        $pedidoReponse = Pedido::where('id', $pedido->id)->first();

        $tranformer = new FindByIdTransformer();
        $recibo = $tranformer->transform($pedidoReponse);
        $pdf = Pdf::loadView('pdf.recibo', ['recibo' => $recibo]);

        return $pdf->stream();
    }

    public function creditCard($calculo, $token, $nombreCliente)
    {
        try {
            // Validar token antes de procesar
            if (empty($token)) {
                Log::error('Clover creditCard - Token vacío');
                return response()->json([
                    'error' => [
                        'message' => 'Token de tarjeta no proporcionado',
                        'code' => 'missing_token'
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validar formato del token
            $token = trim($token);
            if (!preg_match('/^clv_/', $token)) {
                Log::error('Clover creditCard - Token con formato inválido', [
                    'token_preview' => substr($token, 0, 30),
                    'token_length' => strlen($token)
                ]);
                return response()->json([
                    'error' => [
                        'message' => 'Token de tarjeta inválido. El token debe comenzar con "clv_"',
                        'code' => 'invalid_token_format'
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }

            $ch = curl_init();

            $data = [
                "amount" => (int)$calculo, // Asegurar que sea entero
                "currency" => "usd",
                "source" => $token, // El token de Clover
                "description" => $nombreCliente ?: "Pago desde tienda online"
            ];

            // Obtener configuración de Clover según el entorno
            // $environment = config('clover.environment', 'production');
            // $cloverConfig = config("clover.{$environment}");

            $environment = 'production';
            $cloverConfig = "9f0919d8-6bc3-d88b-2bee-fcd1102b4b6a";
            // Intentar obtener el API key desde la BD primero, si no existe usar el del config
            $configService = app(\App\Services\CloverConfigService::class);

            //$apiKey = $configService->getApiKey($environment) ?: $cloverConfig['api_key'];
            $apiKey = $cloverConfig;

            // Validar que el API key esté configurado
            if (empty($apiKey)) {
                Log::error('Clover creditCard - API key no configurado', [
                    'environment' => $environment
                ]);
                return response()->json([
                    'error' => [
                        'message' => 'Configuración de Clover incompleta. API key no configurado.',
                        'code' => 'missing_api_key'
                    ]
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            //$apiUrl = $cloverConfig['api_url'] . '/charges';
            $apiUrl = 'https://scl.clover.com/v1/charges';

            // IMPORTANTE: Verificar que el token sea del mismo entorno
            // Los tokens de producción comienzan con clv_ pero son específicos del entorno
            // Si el token fue generado en producción pero estamos en sandbox (o viceversa), fallará
            // Esto se detecta cuando Clover retorna "invalid source or token"

            // Log para debugging (siempre en sandbox, opcional en producción)
            Log::info('Clover Payment Request', [
                'url' => $apiUrl,
                'amount' => $calculo,
                'token_length' => strlen($token),
                'token_preview' => substr($token, 0, 20) . '...',
                'token_starts_with_clv' => strpos($token, 'clv_') === 0,
                'environment' => $environment,
                'api_key_preview' => substr($apiKey, 0, 10) . '...',
                'api_key_length' => strlen($apiKey),
                'request_data' => $data,
                'note' => 'IMPORTANTE: El token debe ser generado en el mismo entorno (' . $environment . ')'
            ]);

            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $headers = [];
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer ' . $apiKey;
            $headers[] = 'idempotency-key: ' . $this->gen_uuid();
            $headers[] = 'Content-Type: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Medir tiempo real de la llamada a Clover
            $tiempoInicioClover = microtime(true);
            $response = curl_exec($ch);
            $tiempoFinClover = microtime(true);
            $duracionClover = ($tiempoFinClover - $tiempoInicioClover) * 1000; // en milisegundos

            // Registrar tiempo de Clover en el log
            $fecha = date('Y-m-d');
            $logFile = storage_path('logs/pago_web_tiempos_' . $fecha . '.txt');
            $timestamp = date('H:i:s');
            $linea = sprintf(
                "[%s] 7.0.1. Llamada a Clover API completada | Duración de la llamada: %.2fms\n",
                $timestamp,
                $duracionClover
            );
            file_put_contents($logFile, $linea, FILE_APPEND | LOCK_EX);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $responseClover = json_decode($response, true);

            // Log respuesta siempre para debugging
            Log::info('Clover Payment Response', [
                'http_code' => $httpCode,
                'environment' => $environment,
                'has_error' => isset($responseClover['error']),
                'response_preview' => isset($responseClover['error'])
                    ? $responseClover['error']
                    : (isset($responseClover['id']) ? ['id' => $responseClover['id'], 'status' => $responseClover['status'] ?? 'unknown'] : 'unknown')
            ]);

            if (curl_errno($ch)) {
                $curlError = curl_error($ch);
                Log::error('Clover cURL Error', [
                    'error' => $curlError,
                    'environment' => $environment
                ]);
                LogHelper::get('Clover cURL Error: ' . $curlError);
                curl_close($ch);
                return response()->json([
                    'error' => [
                        'message' => 'Error de conexión con Clover: ' . $curlError,
                        'code' => 'connection_error'
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
            curl_close($ch);

            // Si hay error en la respuesta, retornarlo con el código HTTP correcto
            if (isset($responseClover['error'])) {
                $errorDetails = $responseClover['error'];
                $errorMessage = is_array($errorDetails)
                    ? ($errorDetails['message'] ?? $errorDetails['type'] ?? json_encode($errorDetails))
                    : $errorDetails;

                Log::error('Clover API Error', [
                    'environment' => $environment,
                    'error' => $errorDetails,
                    'http_code' => $httpCode,
                    'api_url' => $apiUrl,
                    'api_key_preview' => substr($apiKey, 0, 15) . '...',
                    'request_data' => [
                        'amount' => $calculo,
                        'token_preview' => substr($token, 0, 20) . '...',
                        'token_length' => strlen($token),
                        'token_starts_with_clv' => strpos($token, 'clv_') === 0
                    ],
                    'full_response' => $responseClover
                ]);

                // Retornar el error con el código HTTP de Clover
                $errorCode = $httpCode >= 400 ? $httpCode : Response::HTTP_BAD_REQUEST;

                // Mensaje más amigable para el usuario
                $userMessage = $errorMessage;
                if (strpos($errorMessage, 'invalid source') !== false || strpos($errorMessage, 'valid source') !== false) {
                    // Este error generalmente significa que el token fue generado en un entorno diferente
                    $userMessage = 'El token de tarjeta no es válido. Esto puede ocurrir si el token fue generado en un entorno diferente (sandbox vs producción). Por favor, verifique que el frontend y backend estén usando el mismo entorno.';
                } elseif (strpos($errorMessage, '401') !== false || strpos($errorMessage, 'Unauthorized') !== false) {
                    $userMessage = 'Error de autenticación con Clover. Verifique la configuración del API key.';
                } elseif ($httpCode == 404) {
                    $userMessage = 'Error: El token no es válido o fue generado en un entorno diferente. Verifique que el frontend y backend estén sincronizados (ambos en sandbox o ambos en producción).';
                }

                return response()->json([
                    'error' => [
                        'message' => $userMessage,
                        'code' => is_array($errorDetails) ? ($errorDetails['code'] ?? 'clover_error') : 'clover_error',
                        'details' => $errorDetails
                    ]
                ], $errorCode);
            }

            // Si el código HTTP no es exitoso pero no hay error en el body, crear un error
            if ($httpCode < 200 || $httpCode >= 300) {
                Log::error('Clover API HTTP Error', [
                    'http_code' => $httpCode,
                    'response' => $responseClover,
                    'environment' => $environment
                ]);

                return response()->json([
                    'error' => [
                        'message' => $responseClover['message'] ?? 'Error al procesar el pago con Clover',
                        'code' => 'clover_api_error',
                        'http_code' => $httpCode
                    ]
                ], $httpCode >= 400 ? $httpCode : Response::HTTP_BAD_REQUEST);
            }

            return response()->json($responseClover, Response::HTTP_OK);
        } catch (\Exception $e) {
            LogHelper::get('Clover Exception: ' . $e->getMessage());
            Log::error('Clover Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function gen_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Ejecuta tareas pesadas de forma asíncrona
     * Si hay queue configurado, usa Jobs. Si no, ejecuta después de la respuesta.
     *
     * @param int $pedidoId
     * @param int $clienteId
     * @param int $cantidad
     * @param float $subtotal
     * @param float $descuentos
     * @param float $total
     * @param string $direccionEnvio
     */
    protected function ejecutarTareasAsincronas($pedidoId, $clienteId, $cantidad, $subtotal, $descuentos, $total, $direccionEnvio)
    {
        $queueConnection = config('queue.default', 'sync');

        // Si hay queue configurado (no sync), usar Jobs
        if ($queueConnection !== 'sync') {
            // ✅ Usar Jobs asíncronos si hay queue configurado
            $this->registrarTiempo('13.1. Despachar Job GenerarProforma', $clienteId, null);
            dispatch(new GenerarProformaJob($pedidoId));
            $this->registrarTiempo('13.2. Despachar Job EnviarWebhookGHL', $clienteId, null);
            dispatch(new EnviarWebhookGHLJob($pedidoId, $clienteId, $cantidad, $subtotal, $descuentos, $total, $direccionEnvio));

            Log::info('PagoWebService::ejecutarTareasAsincronas - Jobs enviados a cola', [
                'pedido_id' => $pedidoId,
                'queue_connection' => $queueConnection
            ]);
        } else {
            // ✅ Si no hay queue, ejecutar después de la respuesta HTTP
            // Esto permite que el usuario reciba la respuesta inmediatamente
            // y las tareas pesadas se ejecutan después
            $service = $this; // Para usar dentro de la closure
            register_shutdown_function(function() use ($pedidoId, $clienteId, $cantidad, $subtotal, $descuentos, $total, $direccionEnvio, $service) {
                // Reiniciar contador para medir las tareas de shutdown
                self::$tiempoInicio = microtime(true);
                self::$tiempoAnterior = self::$tiempoInicio;

                $service->registrarTiempo('=== INICIO TAREAS ASÍNCRONAS (SHUTDOWN) ===', $clienteId, null);

                try {
                    // Generar PDF
                    $service->registrarTiempo('13.1. Iniciar Generación de PDF', $clienteId, null);
                    $proformaService = new ProformaService();
                    $proformaService->proformaParaEmail($pedidoId);
                    $service->registrarTiempo('13.1.1. PDF Generado', $clienteId, null);

                    Log::info('PagoWebService::ejecutarTareasAsincronas - PDF generado en shutdown', [
                        'pedido_id' => $pedidoId
                    ]);
                } catch (\Exception $e) {
                    $service->registrarTiempo('ERROR: Generación de PDF - ' . $e->getMessage(), $clienteId, null);
                    Log::error('PagoWebService::ejecutarTareasAsincronas - Error generando PDF en shutdown', [
                        'pedido_id' => $pedidoId,
                        'error' => $e->getMessage()
                    ]);
                }

                try {
                    // Enviar webhook usando el nuevo EmailService
                    $service->registrarTiempo('13.2. Iniciar Envío de Email y Webhook', $clienteId, null);
                    $cliente = Cliente::find($clienteId);

                    $emailService = app(\App\Services\Email\EmailService::class);
                    $result = $emailService->sendWebhook(
                        config('email.gohighlevel.webhooks.compra_confirmacion'),
                        [
                            'email' => $cliente->email ?? '',
                            'emailCCO' => config('email.default_recipients.doralice'),
                            'pedidoNumero' => $pedidoId,
                            'totalArticulos' => $cantidad,
                            'subtotal' => $subtotal,
                            'costoEnvio' => CalcEnvioHelper::calcular($cantidad),
                            'descuentos' => $descuentos ?: '',
                            'total' => $total,
                            'fecha' => date('Y-m-d'),
                            'direccionEnvio' => $direccionEnvio,
                            'metodoPago' => 'Tarjeta de crédito',
                        ]
                    );

                    if ($result->isSuccess()) {
                        $service->registrarTiempo('13.2.1. Email y Webhook Enviados', $clienteId, null);
                        Log::info('PagoWebService::ejecutarTareasAsincronas - Webhook enviado en shutdown', [
                            'pedido_id' => $pedidoId,
                            'service' => $result->service
                        ]);
                    } else {
                        throw new \Exception('Error al enviar webhook: ' . $result->message);
                    }
                } catch (\Exception $e) {
                    $service->registrarTiempo('ERROR: Envío Email/Webhook - ' . $e->getMessage(), $clienteId, null);
                    Log::error('PagoWebService::ejecutarTareasAsincronas - Error enviando webhook en shutdown', [
                        'pedido_id' => $pedidoId,
                        'error' => $e->getMessage()
                    ]);
                }

                $service->registrarTiempo('=== FIN TAREAS ASÍNCRONAS (SHUTDOWN) ===', $clienteId, null);
            });

            Log::info('PagoWebService::ejecutarTareasAsincronas - Tareas programadas para ejecutar después de respuesta', [
                'pedido_id' => $pedidoId,
                'queue_connection' => $queueConnection
            ]);
        }
    }
}

