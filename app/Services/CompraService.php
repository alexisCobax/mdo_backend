<?php

namespace App\Services;

use Error;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Compradetalle;
use Illuminate\Http\Response;
use App\Models\Compradetallenn;
use Illuminate\Support\Facades\DB;
use App\Filters\Compras\ComprasFilters;
use App\Filters\Compras\ComprasProductoFilters;
use App\Transformers\Compra\FindByIdTransformer;
use App\Services\ProformaService;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class CompraService
{
    public function findAll(Request $request)
    {

        try {
            if ($request->codigo) {
                $data = ComprasProductoFilters::getPaginateCompras($request, Compra::class);
            } else {
                $data = ComprasFilters::getPaginateCompras($request, Compra::class);
            }


            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener las compras', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        try {
            $transformer = new FindByIdTransformer($request);
            $transformer = $transformer->transform();

            return response()->json(['data' => $transformer], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener la compra'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {

        $compra = new Compra();

        if (!$compra) {
            return response()->json(['error' => 'Failed to create compra'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $compra->proveedor = $request->proveedor;
        $compra->fechaDeIngreso = $request->fechaDeIngreso;
        $compra->fechaDePago = $request->fechaDePago;
        $compra->precio = $request->precio;
        $compra->numeroLote = $request->numeroLote;
        $compra->observaciones = $request->observaciones;
        $compra->pagado = $request->pagado;
        $compra->enDeposito = 0;
        $compra->save();
        $compraId = $compra->id;
        $precio = 0;
        if ($request->productos) {
            foreach ($request->productos as $p) {
                Producto::where('id', $p['producto'])->update(['borrado' => null]);
                $precio += $p['precioUnitario'] * $p['cantidad'];
                $compraDetalle = new Compradetalle();
                $compraDetalle->compra = $compraId;
                $compraDetalle->producto = $p['producto'];
                $compraDetalle->cantidad = $p['cantidad'];
                if ($p['precioUnitario'] == "") {
                    $producto = Producto::where('id', $p['producto'])->first();
                    $compraDetalle->precioUnitario = $producto->costo;
                } else {
                    $compraDetalle->precioUnitario = $p['precioUnitario'];
                }
                $compraDetalle->enDeposito = 0;
                if ($p['precioVenta'] == "") {
                    $producto = Producto::where('id', $p['producto'])->first();
                    $compraDetalle->precioVenta = $producto->precio;
                } else {
                    $compraDetalle->precioVenta = $p['precioVenta'];
                }
                $compraDetalle->save();
            }
        }

        if ($request->gastos) {
            foreach ($request->gastos as $g) {
                $precio += $g['precioGasto'];
                $compraGastos = new Compradetallenn();
                $compraGastos->descripcion = $g['descripcion'];
                $compraGastos->precio = $g['precioGasto'];
                $compraGastos->idCompra = $compraId;
                $compraGastos->save();
            }
        }

        $compraPrecio = Compra::where('id', $compraId)->first();
        $compraPrecio->precio = $precio;
        $compraPrecio->save();

        $sql = "UPDATE compradetalle
        LEFT JOIN producto ON compradetalle.producto = producto.id
        SET producto.precio = compradetalle.precioVenta
        WHERE compradetalle.compra = {$compraId}";

        try {
            DB::statement($sql);
        } catch (Error $e) {
            return response()->json("Error: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }


        return response()->json($compra, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        $compra = Compra::find($request->id);

        if (!$compra) {
            return response()->json(['error' => 'Compra not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            // Sacamos todo el stock de los productos ingresados a deposito
            DB::update("
            UPDATE compradetalle
            LEFT JOIN producto ON compradetalle.producto = producto.id
            SET producto.stock = producto.stock - compradetalle.cantidad
            WHERE compradetalle.compra = {$request->id} AND compradetalle.enDeposito = 1
        ");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $compra->proveedor = $request->proveedor;
        $compra->fechaDeIngreso = $request->fechaDeIngreso;
        $compra->fechaDePago = $request->fechaDePago;
        $compra->precio = $request->precio;
        $compra->numeroLote = $request->numeroLote;
        $compra->observaciones = $request->observaciones;
        $compra->pagado = $request->pagado;
        $compra->enDeposito = $request->enDeposito;
        $compra->save();
        $precio = 0;
        if ($request->productos) {

            try {
                CompraDetalle::where('compra', $request->id)->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar los detalles de compra.']);
            }
            $compra->enDeposito = 1;
            foreach ($request->productos as $p) {
                $precio += $p['precioUnitario'] * $p['cantidad'];
                $compraDetalle = new Compradetalle();
                $compraDetalle->compra = $request->id;
                $compraDetalle->producto = $p['producto'];
                $compraDetalle->cantidad = $p['cantidad'];
                if ($p['precioUnitario'] == "") {
                    $producto = Producto::where('id', $p['producto'])->first();
                    $compraDetalle->precioUnitario = $producto->costo;
                } else {
                    $compraDetalle->precioUnitario = $p['precioUnitario'];
                }
                $compraDetalle->enDeposito = $p['enDeposito'];
                if ($p['precioVenta'] == "") {
                    $producto = Producto::where('id', $p['producto'])->first();
                    $compraDetalle->precioVenta = $producto->precio;
                } else {
                    $compraDetalle->precioVenta = $p['precioVenta'];
                }
                $compraDetalle->save();
                if ($p['enDeposito'] == 0) {
                    $compra->enDeposito = 0;
                }
            }
            $compra->save();
        }

        if ($request->gastos) {

            try {
                CompraDetallenn::where('idCompra', $request->id)->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => $request->id . ' Error al eliminar los detalles NN de compra. ' . $e->getMessage()]);
            }

            foreach ($request->gastos as $g) {
                $precio += $g['precioGasto'];
                $compraDetallenn = new Compradetallenn();
                $compraDetallenn->idCompra = $request->id;
                $compraDetallenn->descripcion = $g['descripcion'];
                $compraDetallenn->precio = $g['precioGasto'];
                $compraDetallenn->save();
            }
        } else {
            try {
                CompraDetallenn::where('idCompra', $request->id)->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => $request->id . ' Error al eliminar los detalles NN de compra. ' . $e->getMessage()]);
            }
        }

        $compraPrecio = Compra::where('id', $request->id)->first();
        $compraPrecio->precio = $precio;
        $compraPrecio->save();

        try {
            DB::update("
            UPDATE  compradetalle
	        LEFT JOIN producto on compradetalle.producto = producto.id
		    SET producto.stock = producto.stock + compradetalle.cantidad
	        WHERE compradetalle.compra = {$request->id} and compradetalle.enDeposito= 1;
        ");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $sql = "UPDATE compradetalle
        LEFT JOIN producto ON compradetalle.producto = producto.id
        SET producto.precio = compradetalle.precioVenta
        WHERE compradetalle.compra = {$request->id}";

        try {
            DB::statement($sql);
        } catch (Error $e) {
            return response()->json("Error: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($compra, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $compra = Compra::find($request->id);

        if (!$compra) {
            return response()->json(['error' => 'Compra not found'], Response::HTTP_NOT_FOUND);
        }

        $compra->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function compraEmail(Request $request){
        
        // LOG INICIAL - Verificar que el método se ejecuta (múltiples métodos)
        error_log('========================================');
        error_log('CompraService::compraEmail - INICIO DEL MÉTODO (error_log)');
        error_log('CompraService::compraEmail - Request recibido: ' . json_encode($request->all()));
        error_log('========================================');
        
        Log::info('========================================');
        Log::info('CompraService::compraEmail - INICIO DEL MÉTODO');
        Log::info('CompraService::compraEmail - Request recibido: ' . json_encode($request->all()));
        Log::info('========================================');

        $service = new PagoWebService();

        $carrito = $service->createNotCreditCard($request);
        
        // Obtener el ID del pedido de la respuesta
        $carritoData = json_decode($carrito->getContent(), true);
        $pedidoId = $carritoData['pedidoId'] ?? null;

        // Log para debugging
        Log::info('CompraService::compraEmail - PedidoId obtenido: ' . ($pedidoId ?? 'NULL'));
        Log::info('CompraService::compraEmail - Respuesta completa createNotCreditCard: ' . json_encode($carritoData));

        // Generar la proforma solo si tenemos el ID del pedido
        $proformaLink = null;
        if ($pedidoId) {
            try {
                $proformaService = new ProformaService();
                $proformaService->proformaParaEmail($pedidoId);
                
                // Construir la URL de descarga de la proforma
                $appUrl = env('APP_URL', 'http://localhost');
                $proformaLink = 'https://phpstack-1091339-3819555.cloudwaysapps.com/api/pdf/proforma/' . $pedidoId;
                
                Log::info('CompraService::compraEmail - Proforma generada exitosamente. Link: ' . $proformaLink);
            } catch (\Exception $e) {
                // Si hay error generando la proforma, continuamos sin el link
                Log::error('Error generando proforma: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }
        } else {
            Log::error('CompraService::compraEmail - ERROR: No se pudo obtener el pedidoId de la respuesta');
            Log::error('CompraService::compraEmail - Respuesta completa createNotCreditCard: ' . json_encode($carritoData));
        }

        /* Envio email a cliente **/
        try {

            $curl = curl_init();
            
            // Construir el payload base con el email
            $payload = [
                'email' => $request->email,
            ];
            
            // Solo agregar proformaLink y pedidoId si realmente existen
            if (!empty($pedidoId) && !empty($proformaLink)) {
                $payload['proformaLink'] = $proformaLink;
                $payload['pedidoId'] = $pedidoId;
                Log::info('CompraService::compraEmail - ✅ Payload CON proforma y pedidoId: ' . json_encode($payload, JSON_UNESCAPED_SLASHES));
            } else {
                if (empty($pedidoId)) {
                    Log::error('CompraService::compraEmail - ⚠️ No se enviará proformaLink ni pedidoId porque no hay pedidoId');
                } else if (empty($proformaLink)) {
                    Log::warning('CompraService::compraEmail - ⚠️ No se enviará proformaLink porque falló la generación, pero sí se enviará pedidoId');
                    $payload['pedidoId'] = $pedidoId;
                }
                Log::info('CompraService::compraEmail - Payload final: ' . json_encode($payload, JSON_UNESCAPED_SLASHES));
            }
            
            // CÓDIGO ORIGINAL (comentado para pruebas):
            // if (!empty($proformaLink) && !empty($pedidoId)) {
            //     $payload['proformaLink'] = $proformaLink;
            //     $payload['pedidoId'] = $pedidoId;
            //     Log::info('CompraService::compraEmail - ✅ Payload CON proforma: ' . json_encode($payload, JSON_UNESCAPED_SLASHES));
            // } else {
            //     // Si no hay proformaLink, al menos intentar enviar el pedidoId si existe
            //     if (!empty($pedidoId)) {
            //         $payload['pedidoId'] = $pedidoId;
            //         Log::warning('CompraService::compraEmail - ⚠️ Payload sin proformaLink pero con pedidoId: ' . json_encode($payload, JSON_UNESCAPED_SLASHES));
            //     } else {
            //         Log::warning('CompraService::compraEmail - ⚠️ Payload sin proforma (solo email): ' . json_encode($payload, JSON_UNESCAPED_SLASHES));
            //     }
            // }
            
            // Log final del payload que se enviará
            Log::info('CompraService::compraEmail - 📤 Payload final que se enviará al webhook: ' . json_encode($payload, JSON_UNESCAPED_SLASHES));

            // Preparar el JSON del payload
            $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
            
            // Log del payload JSON que se enviará
            Log::info('CompraService::compraEmail - 🚀 Enviando al webhook. Payload JSON: ' . $payloadJson);
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/lePUNpSmeUT55aL0evkC',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $payloadJson,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));

          $response = curl_exec($curl);
          $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
          
          Log::info('CompraService::compraEmail - Respuesta del webhook GoHighLevel. HTTP Code: ' . $httpCode);
          Log::info('CompraService::compraEmail - Respuesta: ' . $response);

          curl_close($curl);

          if ($httpCode >= 200 && $httpCode < 300) {
              return response()->json([
                  'Response' => 'Enviado Correctamente', 
                  'pedidoId' => $pedidoId, 
                  'proformaLink' => $proformaLink,
                  'webhookResponse' => json_decode($response, true)
              ], Response::HTTP_OK);
          } else {
              Log::error('CompraService::compraEmail - Error en webhook. HTTP Code: ' . $httpCode . ', Response: ' . $response);
              return response()->json([
                  'Response' => 'Enviado con advertencias', 
                  'pedidoId' => $pedidoId, 
                  'proformaLink' => $proformaLink,
                  'error' => 'El webhook retornó código: ' . $httpCode,
                  'webhookResponse' => json_decode($response, true)
              ], Response::HTTP_OK);
          }
          } catch (\Exception $e) {
              Log::error('CompraService::compraEmail - Excepción: ' . $e->getMessage());
              Log::error('CompraService::compraEmail - Stack trace: ' . $e->getTraceAsString());
              return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
          }

  }
}
