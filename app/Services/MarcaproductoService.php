<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Marcaproducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MarcaproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Marcaproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Marcaproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $marcaproducto = Marcaproducto::create($data);

        if (!$marcaproducto) {
            return response()->json(['error' => 'Failed to create Marcaproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($marcaproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $marcaproducto = Marcaproducto::find($request->id);

        if (!$marcaproducto) {
            return response()->json(['error' => 'Marcaproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $marcaproducto->update($request->all());
        $marcaproducto->refresh();

        return response()->json($marcaproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $marcaproducto = Marcaproducto::find($request->id);

        if (!$marcaproducto) {
            return response()->json(['error' => 'Marcaproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $marcaproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
