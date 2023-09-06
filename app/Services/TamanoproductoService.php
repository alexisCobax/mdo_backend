<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Tamanoproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TamanoproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Tamanoproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Tamanoproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $tamanoproducto = Tamanoproducto::create($data);

        if (!$tamanoproducto) {
            return response()->json(['error' => 'Failed to create Tamanoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($tamanoproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $tamanoproducto = Tamanoproducto::find($request->id);

        if (!$tamanoproducto) {
            return response()->json(['error' => 'Tamanoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $tamanoproducto->update($request->all());
        $tamanoproducto->refresh();

        return response()->json($tamanoproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $tamanoproducto = Tamanoproducto::find($request->id);

        if (!$tamanoproducto) {
            return response()->json(['error' => 'Tamanoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $tamanoproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
