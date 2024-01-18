<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\Cotizaciondetalle;
use App\Models\Pedido;
use App\Models\Pedidodetalle;
use App\Models\Producto;
use App\Transformers\CotizacionPedido\CreateCotizacionTransformer;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotizacionPedidoService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {
        //--
    }

    public function create(Request $request)
    {
        $cotizacion = Cotizacion::where('id', $request->cotizacion)->first();
        $cotizacionTransformer = new CreateCotizacionTransformer();
        $cotizacionData = $cotizacionTransformer->transform($cotizacion);

        try {

            $pedido = Pedido::create($cotizacionData);
        } catch (Error $e) {

            return response()->json("error", $e->getMessage());
        }
        $cotizacionDetalles = Cotizaciondetalle::where('cotizacion', $request->cotizacion)->get();

        $detalles = $cotizacionDetalles->map(function ($detalle) use ($pedido) {

            $producto = Producto::where('id', $detalle->producto)->first();
            if ($producto->stock < 0) {
                $detalle->cantidad = 0;
            } else {
                if ($producto->stock < $detalle->cantidad) {
                    $detalle->cantidad = $producto->stock;
                }
            }
            $producto->stock -= $detalle->cantidad;
            $producto->save();

            return [
                'pedido' => $pedido->id,
                'producto' => $detalle->producto,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'costo' => $detalle->precio * $detalle->cantidad
            ];
        });

        try {
            PedidoDetalle::insert($detalles->toArray());
        } catch (Error $e) {

            return response()->json("error", $e->getMessage());
        }

        return response()->json($pedido, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        //   return response()->json($cotizacion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        // return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
