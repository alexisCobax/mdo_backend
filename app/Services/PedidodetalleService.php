<?php

namespace App\Services;

use App\Models\Pedidodetalle;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
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
        $data = Pedidodetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $pedidodetalle = Pedidodetalle::create($data);

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Failed to create Pedidodetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
        $pedidodetalle = Pedidodetalle::find($request->id);

        if (!$pedidodetalle) {
            return response()->json(['error' => 'Pedidodetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
