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

        $carritodetalle = CarritoDetalle::where('carrito', $carrito['id'])
            ->where('producto', $request->producto)->first();

        /*
         *
         * Pregunto si existe en detalle de carrito
         * evaluo para calcular el stock
         *
         */

        if ($carritodetalle) {
            $cantidad = $carritodetalle->cantidad + $request->cantidad;
        } else {
            $cantidad = $request->cantidad;
        }

        $stock = StockHelper::get($cantidad, $request->producto);
        $stock = $stock->getContent();
        $stock = json_decode($stock, true);

        $producto = Producto::where('id', $request->producto)->first();

        $precio = CalcHelper::ListProduct($producto->precio, $producto->precioPromocional);

        /*
         *
         * Verifico si existe el producto en el carro
         * si existe actualizo sino agrego uno nuevo
         *
         */

        if ($carritodetalle) {

            $update = $this->updateProductCart($producto, $carritodetalle, $precio, $carrito['id'], $request, $stock['status'], $producto->stock);

            $carritodetalle->update($update);
            $carritodetalle->refresh();

            $update['status'] = $stock['status'];
            $update['stock'] = $producto->stock;

            return response()->json($update, Response::HTTP_OK);
        } else {
            $create = $this->createProductCart($producto, $precio, $carrito['id'], $request, $stock['status'], $producto->stock);

            $carritodetalle = Carritodetalle::create($create);

            $update['status'] = $stock['status'];
            $update['stock'] = $producto->stock;

            return response()->json($create, Response::HTTP_OK);
        }
    }

    public function updateProductCart($producto, $carritodetalle, $precio, $carrito, $request, $status, $stock)
    {

        $cantidad = $carritodetalle->cantidad + $request->cantidad;

        if (!$status) {

            $cantidad = $producto->stock;

            $payload = [
                'carrito' => $carrito,
                'producto' => $request->producto,
                'precio' => $precio * $cantidad,
                'cantidad' => $cantidad,
            ];
        } else {

            $payload = [
                'carrito' => $carrito,
                'producto' => $request->producto,
                'precio' => $precio * $cantidad,
                'cantidad' => $cantidad,
            ];
        }

        return $payload;
    }

    public function createProductCart($producto, $precio, $carrito, $request, $status, $stock)
    {

        if (!$status) {

            $payload = [
                'carrito' => $carrito,
                'producto' => $request->producto,
                'precio' => $precio * $producto->stock,
                'cantidad' => $producto->stock,
                'status' => $status,
                'stock' => $stock,
            ];
        } else {
            $payload = [
                'carrito' => $carrito,
                'producto' => $request->producto,
                'precio' => $precio * $request->cantidad,
                'cantidad' => $request->cantidad,
                'status' => $status,
                'stock' => $stock,
            ];
        }

        return $payload;
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

        $stock = StockHelper::get($request->cantidad, $request->producto);
        $stock = $stock->getContent();
        $stock = json_decode($stock, true);

        $payload = [
            'carrito' => $carrito['id'],
            'producto' => $request->id,
            'precio' => $precio,
            'cantidad' => $stock['cantidad'],
        ];

        $carritodetalle->update($payload);
        $carritodetalle->refresh();

        $response = [
            'id' => $carritodetalle->id,
            'carrito' => $carritodetalle->carrito,
            'producto' => $carritodetalle->producto,
            'precio' => $carritodetalle->precio,
            'cantidad' => $carritodetalle->cantidad,
            'total' => $carritodetalle->precio * $carritodetalle->cantidad,
            'stockStatus' => $stock['status'],
            'stockMaximo' => $stock['cantidad'],
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
