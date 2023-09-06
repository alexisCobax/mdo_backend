<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Encargadodeventa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EncargadodeventaService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Encargadodeventa::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Encargadodeventa::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $encargadodeventa = Encargadodeventa::create($data);

        if (!$encargadodeventa) {
            return response()->json(['error' => 'Failed to create Encargadodeventa'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($encargadodeventa, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $encargadodeventa = Encargadodeventa::find($request->id);

        if (!$encargadodeventa) {
            return response()->json(['error' => 'Encargadodeventa not found'], Response::HTTP_NOT_FOUND);
        }

        $encargadodeventa->update($request->all());
        $encargadodeventa->refresh();

        return response()->json($encargadodeventa, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $encargadodeventa = Encargadodeventa::find($request->id);

        if (!$encargadodeventa) {
            return response()->json(['error' => 'Encargadodeventa not found'], Response::HTTP_NOT_FOUND);
        }

        $encargadodeventa->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
