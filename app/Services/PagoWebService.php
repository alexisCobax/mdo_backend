<?php

namespace App\Services;

use App\Helpers\CalcTotalHelper;
use App\Helpers\CarritoHelper;
use App\Helpers\LogHelper;
use App\Models\Carrito;
use App\Models\Carritodetalle;
use App\Models\Cupondescuento;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Producto;
use App\Models\Recibo;
use App\Models\Transaccion;
use App\Transformers\Pdf\FindByIdTransformer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PagoWebService
{
    public function create(Request $request)
    {

        $carrito = CarritoHelper::getCarrito();

        $productosCarrito = Carritodetalle::where('carrito', $carrito['id'])->get();

        $pago = $this->creditCard($productosCarrito, $request->token, $carrito);

        $pagoResponse = $pago->getContent();
        $pago = json_decode($pagoResponse);

        /* Guardo Transaccion**/
        $this->saveTransaction($carrito['cliente'], json_encode([]), $pago->status, $pagoResponse);

        /* Si concreto la operacion realizo el guardado de datos **/
        if (isset($pago->paid) && $pago->paid) {

            /** Guardo pedido**/
            $pedido = $this->savePedido($carrito['cliente']);

            //GENERAR RECIBO
            $recibo = [
                'cliente' => $carrito['cliente'],
                'formaDePago' => 2,
                'total' => $pago->amount / 100,
                'observaciones' => 'Pago realizado a traves de la plataforma de clover',
                'pedido' => $pedido->id,
                'garantia' => 0,
                'anulado' => 0,
                'fecha' => NOW(),
            ];

            $recibo = Recibo::create($recibo);

            /* Guardo detalle de pedidos **/
            $this->saveDetallePedido($productosCarrito, $pedido);

            /* Elimino carrito **/
            $carritoUpdate = Carrito::find($carrito['id']);
            $carritoUpdate->estado = 1;
            $carritoUpdate->save();

            if (!$recibo) {
                return response()->json(['error' => 'Failed to create Recibo'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            /* genero y envio el recibo**/
            //$this->sendProforma($pedido);

            return response()->json(['status' => 200, 'mensaje' => 'El pedido fue generado de forma exitosa'], Response::HTTP_OK);
        }

        return response()->json(['error' => $pago], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function savePedido($cliente)
    {
        $pedido = new Pedido;
        $pedido->fecha = NOW();
        $pedido->cliente = $cliente;
        $pedido->estado = 2;
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
        $cantidad = 0;
        $controlStock = false;
        $totalPedido = 0;
        foreach ($productosCarrito as $pc) {
            $controlStock = true;
            $producto = Producto::where('id', $pc['producto'])->first();

            if ($pc['cantidad'] >= $producto['stock']) {
                $cantidad = $producto['stock'];
            } else {
                $cantidad = $pc['cantidad'];
            }

            if ($cantidad <= 0) {
                $controlStock = false;
            }

            if ($controlStock) {

                $totalPedido += $pc['precio'] * $cantidad;

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

                $this->descuentoDeStock($pc['producto'], $cantidad);
            }
        }
        $pedido->total = $totalPedido;
        $pedido->save();
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

    public function creditCard($carritoDetalle, $token, $carrito)
    {

        $totalPorProducto = $carritoDetalle->map(function ($item) {
            return $item->precio * $item->cantidad;
        });

        $subtotal = $totalPorProducto->sum();

        $cantidades = $carritoDetalle->pluck('cantidad');
        $cantidad = $cantidades->sum();

        $cupon = Cupondescuento::where('id', $carrito['cupon'])->firts();
        
        $descuentos = '0.00';

        if($cupon){
            $descuentos = $subtotal * $cupon->descuentoPorcentual / 100;
        }
        $calculo = CalcTotalHelper::calcular($subtotal, $cantidad, $descuentos);
        $calculo = number_format($calculo['totalConEnvio'], 2, '', '');

        try {
            $ch = curl_init();

            //curl_setopt($ch, CURLOPT_URL, 'https://scl-sandbox.dev.clover.com/v1/charges');
            curl_setopt($ch, CURLOPT_URL, 'https://scl.clover.com/v1/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{"amount":' . $calculo . ',"currency":"usd","source":"' . $token . '"}');

            $headers = [];
            $headers[] = 'Accept: application/json';
            //$headers[] = 'Authorization: Bearer 859c0171-ee8b-7c4b-7a07-3a02288fbc03';
            $headers[] = 'Authorization: Bearer 557ccda4-98cb-5aa7-5ea5-39ad96096908';
            $headers[] = 'idempotency-key ' . $this->gen_uuid();
            $headers[] = 'Content-Type: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);

            $responseClover = json_decode($response);

            if (curl_errno($ch)) {
                LogHelper::get(curl_error($ch));
            }
            curl_close($ch);

            return response()->json($responseClover, Response::HTTP_OK);
        } catch (\Exception $e) {
            LogHelper::get($e->getMessage());

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
}
