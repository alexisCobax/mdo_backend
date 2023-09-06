<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Compradetalle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompradetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Compradetalle::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Compradetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $compradetalle = Compradetalle::create($data);

        if (!$compradetalle) {
            return response()->json(['error' => 'Failed to create Compradetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($compradetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $compradetalle = Compradetalle::find($request->id);

        if (!$compradetalle) {
            return response()->json(['error' => 'Compradetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $compradetalle->update($request->all());
        $compradetalle->refresh();

        return response()->json($compradetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $compradetalle = Compradetalle::find($request->id);

        if (!$compradetalle) {
            return response()->json(['error' => 'Compradetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $compradetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
