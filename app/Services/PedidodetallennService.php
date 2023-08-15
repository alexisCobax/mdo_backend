<?php

namespace App\Services;

use App\Models\Pedidodetallenn;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PedidodetallennService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pedidodetallenn::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Pedidodetallenn::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $pedidodetallenn = Pedidodetallenn::create($data);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'Failed to create Pedidodetallenn'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedidodetallenn = Pedidodetallenn::find($request->id);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'Pedidodetallenn not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetallenn->update($request->all());
        $pedidodetallenn->refresh();

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pedidodetallenn = Pedidodetallenn::find($request->id);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'Pedidodetallenn not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetallenn->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
