<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Estadopedido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EstadopedidoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Estadopedido::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Estadopedido::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $estadopedido = Estadopedido::create($data);

        if (!$estadopedido) {
            return response()->json(['error' => 'Failed to create Estadopedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($estadopedido, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $estadopedido = Estadopedido::find($request->id);

        if (!$estadopedido) {
            return response()->json(['error' => 'Estadopedido not found'], Response::HTTP_NOT_FOUND);
        }

        $estadopedido->update($request->all());
        $estadopedido->refresh();

        return response()->json($estadopedido, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $estadopedido = Estadopedido::find($request->id);

        if (!$estadopedido) {
            return response()->json(['error' => 'Estadopedido not found'], Response::HTTP_NOT_FOUND);
        }

        $estadopedido->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
