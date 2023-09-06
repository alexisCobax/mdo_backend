<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Moneda;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MonedaService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Moneda::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Moneda::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $moneda = Moneda::create($data);

        if (!$moneda) {
            return response()->json(['error' => 'Failed to create Moneda'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($moneda, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $moneda = Moneda::find($request->id);

        if (!$moneda) {
            return response()->json(['error' => 'Moneda not found'], Response::HTTP_NOT_FOUND);
        }

        $moneda->update($request->all());
        $moneda->refresh();

        return response()->json($moneda, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $moneda = Moneda::find($request->id);

        if (!$moneda) {
            return response()->json(['error' => 'Moneda not found'], Response::HTTP_NOT_FOUND);
        }

        $moneda->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
