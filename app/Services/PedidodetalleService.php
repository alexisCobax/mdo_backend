<?php

namespace App\Services;

use App\Helpers\StockHelper;
use Illuminate\Http\Request;
use App\Models\Pedidodetalle;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;
use App\Models\Producto;

class PedidodetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pedidodetalle::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Pedidodetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function findByPedidoId(Request $request)
    {
        $pedidoDetalle = Pedidodetalle::where('pedido', $request->id)->get();


        $pedidoDetalleConProductos = $pedidoDetalle->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'idProducto' => $detalle->producto,
                'productoNombre' => optional($detalle->productos)->nombre,
                'productoCosto' => optional($detalle->productos)->costo,
                'precioProducto' => $detalle->precio,
                'cantidadProducto' => $detalle->cantidad
            ];
        });

        return response()->json(['data' => $pedidoDetalleConProductos], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        /*controlo stock*/

        $update = 0;

        $producto = Producto::where('id', $request->producto)->first();

        $stock = $producto->stock - $request->cantidad;

        if ($stock < 0) {
            $update = $producto->stock;
        } else {
            $update = $stock;
        }

        $pedidodetalle = Pedidodetalle::where('pedido', $request->pedido)
            ->where('producto', $request->producto)
            ->first();

        if ($pedidodetalle) {

            $pedidodetalle->cantidad = $pedidodetalle->cantidad + $request->cantidad;

            $pedidodetalle->save();
            if (!$pedidodetalle) {
                return response()->json(['error' => 'Failed to create Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $data = $request->all();
            $pedidodetalle = Pedidodetalle::create($data);

            if (!$pedidodetalle) {
                return response()->json(['error' => 'Failed to create Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $producto->stock = $update;
        $producto->save();

        return response()->json($pedidodetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedidodetalle = Pedidodetalle::find($request->id);

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Pedidodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetalle->update($request->all());
        $pedidodetalle->refresh();

        return response()->json($pedidodetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pedidodetalle = Pedidodetalle::where('id', $request->id)->first();

        $update = 0;

        $producto = Producto::where('id', $pedidodetalle->producto)->first();
        $update = $producto->stock + $pedidodetalle->cantidad;
        $producto->stock = $update;
        $producto->save();

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Pedidodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
