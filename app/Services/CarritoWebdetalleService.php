<?php

namespace App\Services;

use App\Models\Producto;
use App\Helpers\CalcHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Helpers\PaginateHelper;

class CarritoWebdetalleService
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

        $carrito = CarritoHelper::getCarrito(); 

        $data = Carritodetalle::where('carrito',$carrito['id'])->get();

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $carrito = CarritoHelper::getCarrito();

        $productoExistente = Carritodetalle::where('carrito', $carrito['id'])
            ->where('producto', $request->producto)->first();

        if ($productoExistente) {
            $cantidad = $productoExistente->cantidad + $request->cantidad;
            $detalle = [
                "carrito" => $carrito['id'],
                "producto" => $request->producto,
                "precio" => $productoExistente->precio * $cantidad,
                "cantidad" => $cantidad
            ];

            $carritodetalle = Carritodetalle::find($productoExistente->id);

            $carritodetalle->update($detalle);
            $carritodetalle->refresh();
        } else {
            $producto = Producto::find($request->producto);
            $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);

            $detalle = [
                "carrito" => $carrito['id'],
                "producto" => $request->producto,
                "precio" => $precio * $request->cantidad,
                "cantidad" => $request->cantidad
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
