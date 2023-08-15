<?php

namespace App\Services;

use App\Models\Cliente2;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class Cliente2Service
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Cliente2::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Cliente2::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $cliente2 = Cliente2::create($data);

        if (!$cliente2) {
            return response()->json(['error' => 'Failed to create Cliente2'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cliente2, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $cliente2 = Cliente2::find($request->id);

        if (!$cliente2) {
            return response()->json(['error' => 'Cliente2 not found'], Response::HTTP_NOT_FOUND);
        }

        $cliente2->update($request->all());
        $cliente2->refresh();

        return response()->json($cliente2, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cliente2 = Cliente2::find($request->id);

        if (!$cliente2) {
            return response()->json(['error' => 'Cliente2 not found'], Response::HTTP_NOT_FOUND);
        }

        $cliente2->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
