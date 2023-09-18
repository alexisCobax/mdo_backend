<?php

namespace App\Services;

use App\Helpers\CarritoHelper;
use App\Models\Carrito;
use App\Models\Carritodetalle;
use App\Models\Cotizacion;
use App\Models\Cotizaciondetalle;
use App\Transformers\Carrito\FindAllTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarritoWebService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findByToken(Request $request)
    {
        $carrito = CarritoHelper::getCarrito();

        if ($carrito) {
            return $this->findCarritoDetalle($carrito['id']);
        } else {
            $data = [
                'fecha' => NOW(),
                'cliente' => $carrito['cliente'],
                'estado' => 0,
                'vendedor' => 1,
                'formaPago' => 1,
            ];

            $carrito = Carrito::create($data);

            return ['data' => ['carrito' => $carrito['id']]];
        }
    }

    public function findStatus(Request $request)
    {
        $carrito = Carrito::where('cliente', $request->id)
            ->where('estado', 0)
            ->first();

        if ($carrito) {
            return $this->findCarritoDetalle($carrito->id);
        } else {
            $data = [
                'fecha' => NOW(),
                'cliente' => $request->id,
                'estado' => 0,
                'vendedor' => 1,
                'formaPago' => 1,
            ];

            $carrito = Carrito::create($data);

            return ['data' => ['carrito' => $carrito->id]];
        }
    }

    public function findCarritoDetalle($id)
    {
        $transformer = new FindAllTransformer();
        if ($transformer) {
            return response()->json(['data' => $transformer->transform($id)], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'No se encontraron datos'], Response::HTTP_NOT_FOUND);
        }
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $carrito = Carrito::create($data);

        if (!$carrito) {
            return response()->json(['error' => 'Failed to create Carrito'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($carrito, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $carrito = Carrito::find($request->id);

        if (!$carrito) {
            return response()->json(['error' => 'Carrito not found'], Response::HTTP_NOT_FOUND);
        }

        $carrito->update($request->all());
        $carrito->refresh();

        return response()->json($carrito, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $carrito = Carrito::find($request->id);

        if (!$carrito) {
            return response()->json(['error' => 'Carrito not found'], Response::HTTP_NOT_FOUND);
        }

        $carrito->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function procesar(Request $request)
    {
        $total = 0;

        $carrito = CarritoHelper::getCarrito();

        $carritoDetalle = Carritodetalle::where('carrito', $carrito['id'])->get();

        $carrito = Carrito::find($carrito['id'])->first();

        $cotizacion = new Cotizacion;
        $cotizacion->fecha = $carrito->fecha;
        $cotizacion->cliente = $carrito->cliente;
        $cotizacion->total = $carritoDetalle->pluck('precio')->sum();
        $cotizacion->estado = $carrito->estado;
        $cotizacion->descuento = '0.00';
        $cotizacion->save();

        $idCotizacion = $cotizacion->id;
        foreach ($carritoDetalle as $cd) {

            $cotizacion = new Cotizaciondetalle;
            $cotizacion->cotizacion = $idCotizacion;
            $cotizacion->producto = $cd['producto'];
            $cotizacion->precio = $cd['precio'];
            $cotizacion->cantidad = $cd['cantidad'];
            $cotizacion->save();
        }

        $carrito->update(['estado' => 1]);

        if (!$cotizacion) {
            return response()->json(['error' => 'Cotizacion not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $cotizacion], Response::HTTP_OK);
    }
}
