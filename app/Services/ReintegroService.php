<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Reintegro;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReintegroService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Reintegro::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Reintegro::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $reintegro = Reintegro::create($data);

        if (!$reintegro) {
            return response()->json(['error' => 'Failed to create Reintegro'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($reintegro, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $reintegro = Reintegro::find($request->id);

        if (!$reintegro) {
            return response()->json(['error' => 'Reintegro not found'], Response::HTTP_NOT_FOUND);
        }

        $reintegro->update($request->all());
        $reintegro->refresh();

        return response()->json($reintegro, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $reintegro = Reintegro::find($request->id);

        if (!$reintegro) {
            return response()->json(['error' => 'Reintegro not found'], Response::HTTP_NOT_FOUND);
        }

        $reintegro->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
