<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Carritodetalle;
use App\Helpers\PaginateHelper;
use Illuminate\Support\Facades\Auth;
use App\Transformers\Carrito\FindAllTransformer;

class CarritoWebService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findByToken(Request $request)
    {
        $user = Auth::user();

        $cliente = Cliente::where('usuario', $user->id)->first();
        $carrito = Carrito::where('cliente', $cliente->id)->first();
        $detalle = CarritoDetalle::where('carrito', $carrito->id)
        ->selectRaw('SUM(precio) as total_precio, SUM(cantidad) as total_cantidad')
        ->get();

        $data = [
            "estado" => $carrito->estado,
            "total" => $detalle->pluck('total_precio')->first(),
            "cantidad" => $detalle->pluck('total_cantidad')->first()
        ];

        return response()->json(['data' => $data], Response::HTTP_OK);
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
                'formaPago' => 1
            ];

            $carrito = Carrito::create($data);
            return ["data" => ["carrito" => $carrito->id]];
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
}
