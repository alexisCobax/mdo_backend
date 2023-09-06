<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaisService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pais::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Pais::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $pais = Pais::create($data);

        if (!$pais) {
            return response()->json(['error' => 'Failed to create Pais'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pais, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pais = Pais::find($request->id);

        if (!$pais) {
            return response()->json(['error' => 'Pais not found'], Response::HTTP_NOT_FOUND);
        }

        $pais->update($request->all());
        $pais->refresh();

        return response()->json($pais, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $pais = Pais::find($request->id);

        if (!$pais) {
            return response()->json(['error' => 'Pais not found'], Response::HTTP_NOT_FOUND);
        }

        $pais->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
