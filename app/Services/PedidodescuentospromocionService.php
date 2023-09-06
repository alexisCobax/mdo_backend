<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Pedidodescuentospromocion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PedidodescuentospromocionService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pedidodescuentospromocion::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Pedidodescuentospromocion::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $pedidodescuentospromocion = Pedidodescuentospromocion::create($data);

        if (!$pedidodescuentospromocion) {
            return response()->json(['error' => 'Failed to create Pedidodescuentospromocion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidodescuentospromocion, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedidodescuentospromocion = Pedidodescuentospromocion::find($request->id);

        if (!$pedidodescuentospromocion) {
            return response()->json(['error' => 'Pedidodescuentospromocion not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodescuentospromocion->update($request->all());
        $pedidodescuentospromocion->refresh();

        return response()->json($pedidodescuentospromocion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pedidodescuentospromocion = Pedidodescuentospromocion::find($request->id);

        if (!$pedidodescuentospromocion) {
            return response()->json(['error' => 'Pedidodescuentospromocion not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodescuentospromocion->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
