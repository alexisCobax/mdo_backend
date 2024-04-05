<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Pedidodetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $data = Pedidodetalle::where('id', $request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function findByPedidoId(Request $request)
    {
        $pedidoDetalle = Pedidodetalle::where('pedido', $request->id)->get();

        $pedidoDetalleConProductos = $pedidoDetalle->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'idProducto' => $detalle->producto,
                'codigo' => optional($detalle->productos)->codigo,
                'productoNombre' => optional($detalle->productos)->nombre,
                'productoCosto' => optional($detalle->productos)->costo,
                'precioProducto' => $detalle->precio,
                'cantidadProducto' => $detalle->cantidad,
            ];
        });

        return response()->json(['data' => $pedidoDetalleConProductos], Response::HTTP_OK);
    }

    // public function create(Request $request)
    // {

    //     /*controlo stock*/

    //     $producto = Producto::where('id', $request->producto)->first();

    //     if ($producto->stock==0){
    //         return response()->json(['error' => 'Producto sin stock','status'=>500], Response::HTTP_OK);
    //     }

    //     if ($producto->stock < $request->cantidad) {
    //         $request->cantidad = $producto->stock;
    //     }

    //     $pedidodetalle = Pedidodetalle::with('productos')->where('pedido', $request->pedido)
    //         ->where('producto', $request->producto)
    //         ->first();

    //     if ($pedidodetalle) {

    //         $pedidodetalle->cantidad = $pedidodetalle->cantidad + $request->cantidad;

    //         $pedidodetalle->save();
    //         if (!$pedidodetalle) {
    //             return response()->json(['error' => 'Failed to create Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
    //         }
    //         $data = $request->all();
    //         $data['cantidad'] = $request->cantidad;
    //         $pedidodetalle = Pedidodetalle::with('productos')->create($data);

    //         if (!$pedidodetalle) {
    //             return response()->json(['error' => 'Failed to create Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
    //         }
    //     }

    //     $producto->stock = $producto->stock - $request->cantidad;
    //     $producto->save();

    //     return response()->json($pedidodetalle, Response::HTTP_OK);
    // }

    public function create(Request $request)
{
    /*control de stock*/
    
    if($request->codigo){
        $producto = Producto::where('codigo', $request->codigo)->first();
        if(!$producto){
            return response()->json(['error' => 'Producto inexistente', 'status' => 501], Response::HTTP_OK);
        }
        $idProducto = $producto->id;
    }else{
        $producto = Producto::where('id', $request->producto)->first();
        if(!$producto){
            return response()->json(['error' => 'Producto inexistente', 'status' => 501], Response::HTTP_OK);
        }
        $idProducto = $request->producto;
    }

    if ($producto->stock == 0) {
        return response()->json(['error' => 'Producto sin stock', 'status' => 500], Response::HTTP_OK);
    }

    if ($producto->stock < $request->cantidad) {
        $request->cantidad = $producto->stock;
    }

    $pedidodetalle = Pedidodetalle::with('productos')->where('pedido', $request->pedido)
        ->where('producto', $idProducto)
        ->first();

    if ($pedidodetalle) {
        $pedidodetalle->cantidad += $request->cantidad;
        $pedidodetalle->save();

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Failed to update Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } else {
        $data = $request->all();
        $data['cantidad'] = $request->cantidad;
        $pedidodetalle = Pedidodetalle::with('productos')->create($data);

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Failed to create Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    $producto->stock = $producto->stock - $request->cantidad;
    $producto->save();

    if (!$producto) {
        return response()->json(['error' => 'Failed to update Producto'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return response()->json($pedidodetalle, Response::HTTP_OK);
}


    public function update(Request $request)
    {
        $pedidodetalle = Pedidodetalle::where('id', $request->id)->first();

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Pedidodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetalle->update($request->all());
        $pedidodetalle->refresh();

        return response()->json($pedidodetalle, Response::HTTP_OK);
    }

    public function updateProducto(Request $request)
    {

        $cantidadAnterior = 0;
        $nuevaCantidad = 0;
        $stockActual = 0;
        $pedidodetalle = Pedidodetalle::where('id', $request->id)->first();

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Pedidodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $producto = Producto::where('id', $pedidodetalle->producto)->first();

        $cantidadAnterior = $pedidodetalle->cantidad;
        $nuevaCantidad = $request->cantidad;
        $stockActual = $producto->stock;

        if ($stockActual + $cantidadAnterior < $nuevaCantidad) {
            return response()->json(['status' => '404', 'respuesta' => 'Cantidad es mayor que stock, el stock disponible es ' . ($stockActual + $cantidadAnterior)], Response::HTTP_NOT_FOUND);
        }

        $producto->stock = $stockActual + $cantidadAnterior - $nuevaCantidad;
        $producto->save();

        $pedidodetalle->cantidad = $nuevaCantidad;
        $pedidodetalle->precio = $request->precio;
        $pedidodetalle->save();

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
