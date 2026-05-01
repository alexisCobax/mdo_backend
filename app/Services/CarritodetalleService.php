<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Carritodetalle;
use App\Models\Cupondescuento;
use App\Helpers\CalcHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarritodetalleService
{

    public function asignarVendedor(Request $request){

        try {
        $user = Auth::user();

        $carrito = Carrito::where('id', $request->id)->first();
        $carrito->gestionadoPor = $user['id'];
        $carrito->save();
        
        Cliente::where('id', $carrito->cliente)->update(['asesor' => $user['id']]);

        return response()->json(Response::HTTP_OK);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    }

    public function liberarVendedor(Request $request){

        try {

        Carrito::where('id', $request->id)->update(['gestionadoPor' => 0]);

        return response()->json(Response::HTTP_OK);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al asignar el carrito'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    }

    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Carritodetalle::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByIdVendedor(Request $request)
    {
        try {
            $carrito = Carrito::where('id', $request->id)->first();
        
            $cliente = Cliente::where('id', $carrito->cliente)->first();
        
            $clienteDatos = [
                "nombre"    => $cliente->nombre ?? '',
                "email"     => $cliente->email ?? '',
                "telefono"  => $cliente->telefono ?? '',
                "domicilio" => $cliente->domicilio ?? '',
                "whatsapp"  => $cliente->whatsapp ?? ''
            ];
        
            $baseUrl = env('URL_IMAGENES_PRODUCTOS');
        
            $SQL1 = "SELECT 
                    c.precio, 
                    c.cantidad, 
                    p.nombre AS producto, 
                    mp.nombre AS nombremarca,
                    cr.cupon,

                    -- Valor numérico del descuento
                    COALESCE(
                        NULLIF(cp.descuentoFijo, 0),
                        NULLIF(cp.descuentoPorcentual, 0)
                    ) AS descuentoAplicado,

                    -- Tipo del descuento
                    CASE
                        WHEN cp.descuentoFijo IS NOT NULL AND cp.descuentoFijo != 0 
                            THEN 'FIJO'
                        WHEN cp.descuentoPorcentual IS NOT NULL AND cp.descuentoPorcentual != 0 
                            THEN 'PORCENTAJE'
                        ELSE 'NINGUNO'
                    END AS tipoDescuento,

                    -- Símbolo del descuento
                    CASE
                        WHEN cp.descuentoFijo IS NOT NULL AND cp.descuentoFijo != 0 
                            THEN '$'
                        WHEN cp.descuentoPorcentual IS NOT NULL AND cp.descuentoPorcentual != 0 
                            THEN '%'
                        ELSE ''
                    END AS simboloDescuento,

                    IF(fp.url IS NOT NULL, 
                        fp.url, 
                        CONCAT(:baseUrl, p.imagenPrincipal, '.jpg')
                    ) AS imagen,

                    ROUND(c.precio * c.cantidad, 2) AS total,

                    ROUND(
                        CASE
                            WHEN cp.descuentoFijo IS NOT NULL AND cp.descuentoFijo != 0 
                                THEN (c.precio * c.cantidad) - cp.descuentoFijo
                            WHEN cp.descuentoPorcentual IS NOT NULL AND cp.descuentoPorcentual != 0 
                                THEN (c.precio * c.cantidad) * (1 - (cp.descuentoPorcentual / 100))
                            ELSE (c.precio * c.cantidad)
                        END
                    , 2) AS totalConDescuento

                FROM carritodetalle c
                JOIN producto p ON p.id = c.producto
                LEFT JOIN fotoproducto fp ON fp.id = p.imagenPrincipal
                LEFT JOIN marcaproducto mp ON mp.id = p.marca
                LEFT JOIN carrito cr ON c.carrito = cr.id
                LEFT JOIN cupondescuento cp ON cr.cupon = cp.id
                WHERE c.carrito = :carritoId
                ORDER BY c.id DESC";

        
            $rows = DB::select($SQL1, [
                'carritoId' => $request->id,
                'baseUrl'   => $baseUrl,
            ]);
        
            return response()->json([
                'status'  => Response::HTTP_OK,
                'results' => $rows,
                'cliente' => $clienteDatos,
                'gestionadoPor' => $carrito->gestionadoPor ?? '',
                'estado'  => $carrito->estado ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Ocurrió un error al obtener los detalles',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        
    }

    public function findById(Request $request)
    {
        $data = Carritodetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $productoExistente = Carritodetalle::where('carrito', $request->carrito)
            ->where('producto', $request->producto)->get();

        if (count($productoExistente)) {
            $cantidad = $productoExistente[0]['cantidad'] + $request->cantidad;
            $detalle = [
                'carrito' => $request->carrito,
                'producto' => $request->producto,
                'precio' => $productoExistente[0]['precio'] * $cantidad,
                'cantidad' => $cantidad,
            ];

            $carritodetalle = Carritodetalle::find($request->id);

            $carritodetalle->update($detalle);
            $carritodetalle->refresh();
        } else {

            $producto = Producto::find($request->producto);
            $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);

            $detalle = [
                'carrito' => $request->carrito,
                'producto' => $request->producto,
                'precio' => $precio * $request->cantidad,
                'cantidad' => $request->cantidad,
            ];

            $carritodetalle = Carritodetalle::create($detalle);
        }

        if (!$carritodetalle) {
            return response()->json(['error' => 'Failed to create Carritodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($carritodetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $carritodetalle = Carritodetalle::find($request->id);

        if (!$carritodetalle) {
            return response()->json(['error' => 'Carritodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $carritodetalle->update($request->all());
        $carritodetalle->refresh();

        return response()->json($carritodetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $carritodetalle = Carritodetalle::find($request->id);

        if (!$carritodetalle) {
            return response()->json(['error' => 'Carritodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $carritodetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    /**
     * Generate a pedido from carrito (vendedor flow).
     * Validates carrito, calculates totals (with optional cupón), creates Pedido and Pedidodetalle,
     * decrements stock, marks carrito as converted (estado = 1).
     *
     * @param  \Illuminate\Http\Request $request  Must contain 'id' (carrito id)
     * @return \Illuminate\Http\Response
     */
    public function generarPedido(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
            }

            $id = $request->input('id');
            if (!$id) {
                return response()->json(['error' => 'Falta el ID del carrito'], Response::HTTP_BAD_REQUEST);
            }

            $carrito = Carrito::where('id', $id)->first();
            if (!$carrito) {
                return response()->json(['error' => 'Carrito no encontrado'], Response::HTTP_NOT_FOUND);
            }

            if ($carrito->estado == 1) {
                return response()->json(['error' => 'El carrito ya fue convertido en pedido'], Response::HTTP_BAD_REQUEST);
            }

            $lineas = Carritodetalle::where('carrito', $id)->get();
            if ($lineas->isEmpty()) {
                return response()->json(['error' => 'El carrito está vacío'], Response::HTTP_BAD_REQUEST);
            }

            // Recalcular líneas según stock: reducir a máximo disponible o quitar si no hay stock
            $adjustedLineas = [];
            $ajustesLog = [];
            $idsCarritoDetalleToDelete = [];

            foreach ($lineas as $item) {
                $producto = Producto::find($item->producto);
                if (!$producto) {
                    Log::warning('CarritodetalleService::generarPedido - Producto no encontrado, se omite', [
                        'carrito_id' => $id,
                        'producto_id' => $item->producto,
                    ]);
                    $idsCarritoDetalleToDelete[] = $item->id;
                    continue;
                }

                $stock = (int) $producto->stock;
                $cant = (int) $item->cantidad;
                $nombreProducto = $producto->nombre ?? 'ID ' . $producto->id;

                if ($stock >= $cant) {
                    $adjustedLineas[] = [
                        'carritodetalle_id' => $item->id,
                        'producto'         => (int) $item->producto,
                        'precio'           => (float) $item->precio,
                        'cantidad'         => $cant,
                    ];
                } elseif ($stock > 0) {
                    $adjustedLineas[] = [
                        'carritodetalle_id' => $item->id,
                        'producto'         => (int) $item->producto,
                        'precio'           => (float) $item->precio,
                        'cantidad'         => $stock,
                    ];
                    $msg = "Producto \"{$nombreProducto}\" reducido de {$cant} a {$stock} por stock insuficiente";
                    $ajustesLog[] = $msg;
                    Log::info('CarritodetalleService::generarPedido - Ajuste por stock', [
                        'carrito_id'   => $id,
                        'producto_id'  => $producto->id,
                        'producto'     => $nombreProducto,
                        'cantidad_pedida' => $cant,
                        'cantidad_asignada' => $stock,
                    ]);
                } else {
                    $idsCarritoDetalleToDelete[] = $item->id;
                    $msg = "Producto \"{$nombreProducto}\" eliminado del pedido por falta de stock";
                    $ajustesLog[] = $msg;
                    Log::info('CarritodetalleService::generarPedido - Producto sin stock eliminado', [
                        'carrito_id'  => $id,
                        'producto_id' => $producto->id,
                        'producto'    => $nombreProducto,
                    ]);
                }
            }

            if (empty($adjustedLineas)) {
                return response()->json([
                    'error' => 'No hay productos con stock disponible para generar el pedido. Todos los ítems fueron eliminados o reducidos por falta de stock.',
                    'ajustes' => $ajustesLog,
                ], Response::HTTP_BAD_REQUEST);
            }

            // Totales recalculados con las líneas ajustadas
            $subTotal = array_sum(array_map(function ($line) {
                return $line['precio'] * $line['cantidad'];
            }, $adjustedLineas));
            $cantidadTotal = array_sum(array_column($adjustedLineas, 'cantidad'));
            $descuentos = 0.0;
            $cupon = $carrito->cupon ? Cupondescuento::find($carrito->cupon) : null;
            if ($cupon) {
                if ($cupon->descuentoPorcentual) {
                    $descuentos = $subTotal * (float) $cupon->descuentoPorcentual / 100;
                } elseif ($cupon->descuentoFijo) {
                    $descuentos = (float) $cupon->descuentoFijo;
                }
            }

            $totalPedido = round($subTotal - $descuentos, 2);
            $totalEnvio = 0.0;
            $descuentoNeto = round($descuentos, 2);

            $cliente = Cliente::where('id', $carrito->cliente)->first();
            if (!$cliente) {
                return response()->json(['error' => 'Cliente del carrito no encontrado'], Response::HTTP_BAD_REQUEST);
            }

            $pedidoId = null;
            DB::beginTransaction();
            try {
                // Sincronizar carrito: eliminar líneas sin stock y actualizar cantidades reducidas
                if (!empty($idsCarritoDetalleToDelete)) {
                    Carritodetalle::whereIn('id', $idsCarritoDetalleToDelete)->delete();
                }
                foreach ($adjustedLineas as $line) {
                    Carritodetalle::where('id', $line['carritodetalle_id'])->update(['cantidad' => $line['cantidad']]);
                }

                $pedido = new Pedido();
                $pedido->fecha = now();
                $pedido->cliente = $carrito->cliente;
                $pedido->estado = 1;
                $pedido->vendedor = $user->id;
                $pedido->formaDePago = $carrito->formaDePago ?? 1;
                $pedido->invoice = 0;
                $pedido->total = $totalPedido + $totalEnvio;
                $pedido->descuentoPorcentual = '0.00';
                $pedido->descuentoNeto = $descuentoNeto;
                $pedido->totalEnvio = $totalEnvio;
                $pedido->origen = 2;
                $pedido->nombreEnvio = $cliente->nombreEnvio ?? null;
                $pedido->domicilioEnvio = $cliente->direccionShape ?? $cliente->direccion ?? null;
                $pedido->paisEnvio = $cliente->paisShape ?? $cliente->pais ?? null;
                $pedido->regionEnvio = $cliente->regionEnvio ?? null;
                $pedido->ciudadEnvio = $cliente->ciudadEnvio ?? $cliente->ciudad ?? null;
                $pedido->cpEnvio = $cliente->cpShape ?? $cliente->codigoPostal ?? null;
                $pedido->transportadoraNombre = $cliente->transportadora ?? null;
                $pedido->telefonoTransportadora = $cliente->telefonoTransportadora ?? null;
                $pedido->observaciones = $cliente->observaciones ?? null;
                $pedido->tipoDeEnvio = $cliente->tipoDeEnvio ?? null;
                $pedido->save();
                $pedidoId = $pedido->id;

                $detalles = [];
                foreach ($adjustedLineas as $line) {
                    $detalles[] = [
                        'pedido'   => $pedidoId,
                        'producto' => $line['producto'],
                        'precio'   => $line['precio'],
                        'cantidad' => $line['cantidad'],
                        'costo'    => 0,
                        'envio'    => 0,
                        'tax'      => 0,
                        'taxEnvio' => 0,
                    ];
                }
                Pedidodetalle::insert($detalles);

                foreach ($adjustedLineas as $line) {
                    Producto::where('id', $line['producto'])->decrement('stock', $line['cantidad']);
                }

                Carrito::where('id', $id)->update(['estado' => 1]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('CarritodetalleService::generarPedido - Error en transacción', [
                    'carrito_id' => $id,
                    'message'   => $e->getMessage(),
                ]);
                return response()->json([
                    'error'   => 'Error al generar el pedido',
                    'message' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $response = [
                'status'   => 'ok',
                'mensaje'   => 'Pedido generado correctamente',
                'pedidoId'  => $pedidoId,
            ];
            if (!empty($ajustesLog)) {
                $response['ajustes'] = $ajustesLog;
            }
            return response()->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('CarritodetalleService::generarPedido', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'No se pudo generar el pedido',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
