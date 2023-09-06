<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Origenpedido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrigenpedidoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Origenpedido::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Origenpedido::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $origenpedido = Origenpedido::create($data);

        if (!$origenpedido) {
            return response()->json(['error' => 'Failed to create Origenpedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($origenpedido, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $origenpedido = Origenpedido::find($request->id);

        if (!$origenpedido) {
            return response()->json(['error' => 'Origenpedido not found'], Response::HTTP_NOT_FOUND);
        }

        $origenpedido->update($request->all());
        $origenpedido->refresh();

        return response()->json($origenpedido, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $origenpedido = Origenpedido::find($request->id);

        if (!$origenpedido) {
            return response()->json(['error' => 'Origenpedido not found'], Response::HTTP_NOT_FOUND);
        }

        $origenpedido->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
