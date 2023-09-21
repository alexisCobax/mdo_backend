<?php

namespace App\Services;

use App\Helpers\CalcHelper;
use App\Helpers\CarritoHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\StockHelper;
use App\Models\Carritodetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $data = [];

        $carrito = CarritoHelper::getCarrito();

        $carritoDetalle = Carritodetalle::where('carrito', $carrito['id'])->get();

        foreach ($carritoDetalle as $c) {

            $precio = CalcHelper::ListProduct(optional($c->productos)->precio, optional($c->productos)->precioPromocional);

            $data[] = [
                'idProducto' => $c->producto,
                'nombreProducto' => optional($c->productos)->nombre,
                'tamanioProducto' => optional($c->productos)->tamano,
                'marcaProducto' => optional(optional($c->productos)->marcas)->nombre,
                'modeloProducto' => '',
                'cantidadProducto' => $c->cantidad,
                'precioUnitario' => $precio,
                'precioTotal' => $precio * $c->cantidad,
            ];
        }

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $carrito = CarritoHelper::getCarrito();

        $producto = Producto::where('id', $request->producto)->first();

        $productoExistente = Carritodetalle::where('carrito', $carrito['id'])
            ->where('producto', $request->producto)->first();

        $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);

        if ($productoExistente) {

            $stock = StockHelper::get($request->cantidad, $request->producto);
            $stock = $stock->getContent();
            $stock = json_decode($stock, true);

            $detalle = [
                'carrito' => $carrito['id'],
                'producto' => $request->producto,
                'precio' => $precio * $stock['cantidad'],
                'cantidad' => $stock['cantidad'],
            ];

            $carritodetalle = Carritodetalle::find($productoExistente->id);

            $carritodetalle->update($detalle);
            $carritodetalle->refresh();
        } else {

            $stock = StockHelper::get($request->cantidad, $request->producto);
            $stock = $stock->getContent();
            $stock = json_decode($stock, true);

            $detalle = [
                'carrito' => $carrito['id'],
                'producto' => $request->producto,
                'precio' => $precio * $stock['cantidad'],
                'cantidad' => $stock['cantidad'],
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
        $producto = Producto::where('id', $request->id)->first();

        $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);

        $carrito = CarritoHelper::getCarrito();
        $carritodetalle = CarritoDetalle::where('producto', $request->id)
            ->where('carrito', $carrito['id'])
            ->first();

        if (!$carritodetalle) {
            return response()->json(['error' => 'Carrito detalle not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = [
            'carrito' => $carrito['id'],
            'producto' => $request->id,
            'precio' => $precio*$request->cantidad,
            'cantidad' => $request->cantidad,
        ];

        $carritodetalle->update($payload);
        $carritodetalle->refresh();

        $response = [
            'id' => $carritodetalle->id,
            'carrito' => $carritodetalle->carrito,
            'producto' => $carritodetalle->producto,
            'precio' => $carritodetalle->precio,
            'cantidad' => $request->cantidad,
            'total' => $carritodetalle->precio,
        ];

        return response()->json($response, Response::HTTP_OK);
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
