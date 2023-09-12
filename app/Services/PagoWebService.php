<?php

namespace App\Services;

use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Producto;
use App\Models\Transaccion;
use App\Transformers\Pdf\FindByIdTransformer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PagoWebService
{

    public function create(Request $request)
    {

        $pago = $this->creditCard($request->amount, $request->token);

        $pagoResponse = $pago->getContent();
        $pago = json_decode($pagoResponse);

        $carrito = CarritoHelper::getCarrito();

        $productosCarrito = Carritodetalle::where('carrito', $carrito['id'])->get();

        /* Si concreto la operacion realizo el guardado de datos **/
        if (isset($pago->paid) && $pago->paid) {

            /** Guardo pedido**/
            $pedido = $this->savePedido($carrito['cliente']);

            /* Guardo detalle de pedidos **/
            $this->saveDetallePedido($productosCarrito, $pedido);

            /* Guardo Transaccion**/
            $this->saveTransaction($carrito['cliente'], json_encode($pedido), $pago->status, $pagoResponse);

            /* genero y envio la proforma**/
            $this->sendProforma($pedido);

            return response()->json('El pedido fue generado de forma exitosa', Response::HTTP_OK);
        }

        return response()->json(['error' => $pago], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function savePedido($cliente)
    {
        $pedido = new Pedido;
        $pedido->fecha = NOW();
        $pedido->cliente = $cliente;
        $pedido->estado = 4;
        $pedido->vendedor = 1;
        $pedido->formaDePago = 2;
        $pedido->invoice = 0;
        $pedido->total = '0.00';
        $pedido->descuentoPorcentual = '0.00';
        $pedido->descuentoNeto = '0.00';
        $pedido->totalEnvio = '0.00';
        $pedido->origen = 1;
        $pedido->save();

        return $pedido;
    }

    public function saveTransaction($cliente, $pedido, $status, $data)
    {

        $transaccion = new Transaccion;
        $transaccion->fecha = NOW();
        $transaccion->cliente = $cliente;
        $transaccion->pedido = $pedido ? $pedido = 0 : $pedido;
        $transaccion->resultado = $status;
        $transaccion->ctr = $data;
        $transaccion->save();
    }

    public function saveDetallePedido($productosCarrito, $pedido)
    {
        $cantidad = '';
        $controlStock = false;
        foreach ($productosCarrito as $pc) {
            $controlStock = true;
            $producto = Producto::find($pc['producto']);

            if ($pc['cantidad'] > $producto['stock']) {
                $cantidad = $producto['stock'];
            } elseif ($pc['cantidad'] < $producto['stock']) {
                $cantidad = $pc['cantidad'];
            } elseif ($producto['stock'] == 0) {
                $controlStock = false;
            }

            if ($controlStock) {
                $pedidoDetalle = new Pedidodetalle;
                $pedidoDetalle->pedido = $pedido->id;
                $pedidoDetalle->producto = $pc['producto'];
                $pedidoDetalle->precio = $pc['precio'];
                $pedidoDetalle->cantidad = $cantidad;
                $pedidoDetalle->costo = '0.00';
                $pedidoDetalle->envio = '0.00';
                $pedidoDetalle->tax = '0.00';
                $pedidoDetalle->taxEnvio = '0.00';
                $pedidoDetalle->save();

                $this->updateStock($pc['producto'], $cantidad);
            }
        }
    }

    public function updateStock($producto, $cantidad)
    {

        $producto = Producto::findOrFail($producto);
        $stock = $producto->stock - $cantidad;
        $producto->stock = $stock;
        $producto->save();
    }

    public function sendProforma($pedido)
    {
        $pedidoReponse = Pedido::where('id', $pedido->id)->first();

        $tranformer = new FindByIdTransformer();
        $proforma = $tranformer->transform($pedidoReponse);
        $pdf = Pdf::loadView('pdf.proforma', ['proforma' => $proforma]);

        return $pdf->stream();
    }

    public function creditCard($amount, $token)
    {
        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://scl-sandbox.dev.clover.com/v1/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{"amount":' . $amount . ',"currency":"usd","source":"' . $token . '"}');

            $headers = [];
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer 859c0171-ee8b-7c4b-7a07-3a02288fbc03';
            $headers[] = 'idempotency-key ' . $this->gen_uuid();
            $headers[] = 'Content-Type: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);

            $response = json_decode($response);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            return response()->json($response, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
}
