<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SesionService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Sesion::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Sesion::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $sesion = Sesion::create($data);

        if (!$sesion) {
            return response()->json(['error' => 'Failed to create Sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($sesion, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $sesion = Sesion::find($request->id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesion not found'], Response::HTTP_NOT_FOUND);
        }

        $sesion->update($request->all());
        $sesion->refresh();

        return response()->json($sesion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $sesion = Sesion::find($request->id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesion not found'], Response::HTTP_NOT_FOUND);
        }

        $sesion->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
