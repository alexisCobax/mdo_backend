<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Tipodeenvio;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TipodeenvioService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Tipodeenvio::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Tipodeenvio::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $tipodeenvio = Tipodeenvio::create($data);

        if (!$tipodeenvio) {
            return response()->json(['error' => 'Failed to create Tipodeenvio'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($tipodeenvio, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $tipodeenvio = Tipodeenvio::find($request->id);

        if (!$tipodeenvio) {
            return response()->json(['error' => 'Tipodeenvio not found'], Response::HTTP_NOT_FOUND);
        }

        $tipodeenvio->update($request->all());
        $tipodeenvio->refresh();

        return response()->json($tipodeenvio, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $tipodeenvio = Tipodeenvio::find($request->id);

        if (!$tipodeenvio) {
            return response()->json(['error' => 'Tipodeenvio not found'], Response::HTTP_NOT_FOUND);
        }

        $tipodeenvio->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
