<?php

namespace App\Http\Controllers;

use App\Services\ReciboService;
use Illuminate\Http\Request;

class ReciboController extends Controller
{
    private $service;

    public function __construct(ReciboService $ReciboService)
    {
        $this->service = $ReciboService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->service->findAll($request);
    }

        /**
     * Display a listing of the resource.
     *
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function recibosParaVendedores(Request $request)
    {
        return $this->service->recibosParaVendedores($request);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->service->findById($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->service->create($request);
    }

    /**
     * Creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function createOne(Request $request)
    {
        return $this->service->createOne($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return $this->service->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  use App\Services\ReciboService $service
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        return $this->service->delete($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * TEMPORAL: Genera recibo por transacción y pedido
     * Endpoint simple para generar recibo con transaccion_id y pedido_id
     * Si el recibo no existe, lo crea automáticamente
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generarPorTransaccionPedido(Request $request)
    {
        $transaccionId = $request->input('transaccion_id');
        $pedidoId = $request->input('pedido_id');

        if (!$pedidoId) {
            return response()->json(['error' => 'pedido_id es requerido'], 400);
        }

        // Buscar recibo por pedido_id
        $recibo = \App\Models\Recibo::where('pedido', $pedidoId)->first();

        // Si no existe, crearlo
        if (!$recibo) {
            // Buscar el pedido
            $pedido = \App\Models\Pedido::find($pedidoId);
            if (!$pedido) {
                return response()->json(['error' => 'No se encontró el pedido ' . $pedidoId], 404);
            }

            // Intentar obtener datos de la transacción si existe
            $totalRecibo = $pedido->total ?? 0;
            $observaciones = 'Pago realizado a traves de la plataforma de clover';

            if ($transaccionId) {
                $transaccion = \App\Models\Transaccion::find($transaccionId);
                if ($transaccion) {
                    // Prioridad 1: Si hay payload con calculos, usar ese total
                    if ($transaccion->payload && is_array($transaccion->payload)) {
                        if (isset($transaccion->payload['calculos']['total'])) {
                            // El total en payload ya está en formato decimal, no centavos
                            $totalRecibo = $transaccion->payload['calculos']['total'];
                        } elseif (isset($transaccion->payload['calculos']['totalConEnvio'])) {
                            $totalRecibo = $transaccion->payload['calculos']['totalConEnvio'];
                        }
                    }
                    
                    // Prioridad 2: Intentar obtener el amount del resultado de la transacción (Clover)
                    if ($totalRecibo == 0 || $totalRecibo == $pedido->total) {
                        $resultado = json_decode($transaccion->resultado, true);
                        if (is_array($resultado) && isset($resultado['amount'])) {
                            $totalRecibo = $resultado['amount'] / 100; // Clover devuelve en centavos
                        }
                    }
                }
            }

            // Si aún no tenemos total, usar el del pedido
            if ($totalRecibo == 0) {
                $totalRecibo = $pedido->total ?? 0;
            }

            // Crear el recibo
            try {
                // La fecha debe ser de ayer
                $fechaRecibo = date('Y-m-d', strtotime('-1 day'));
                
                $recibo = \App\Models\Recibo::create([
                    'cliente' => $pedido->cliente,
                    'formaDePago' => $pedido->formaDePago ?? 2, // 2 = tarjeta por defecto
                    'total' => $totalRecibo,
                    'observaciones' => $observaciones,
                    'pedido' => $pedidoId,
                    'garantia' => 0,
                    'anulado' => 0,
                    'fecha' => $fechaRecibo,
                    'invoice' => $pedido->invoice ?? 0,
                ]);

                // Refrescar para asegurar que tenemos el ID
                $recibo->refresh();

                if (!$recibo || !$recibo->id) {
                    return response()->json(['error' => 'Error al crear el recibo - no se obtuvo ID'], 500);
                }

                // Verificar que se guardó correctamente buscándolo de nuevo
                $reciboVerificado = \App\Models\Recibo::find($recibo->id);
                if (!$reciboVerificado) {
                    return response()->json(['error' => 'Error: El recibo se creó pero no se encontró en la BD'], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Error al crear el recibo',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        // Usar el método existente para generar el PDF
        $request->merge(['id' => $recibo->id]);
        return $this->service->findById($request);
    }
}
