<?php

namespace App\Services;

use App\Helpers\CalcHelper;
use App\Helpers\PaginateHelper;
use App\Models\Carritodetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarritodetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Carritodetalle::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
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
}
