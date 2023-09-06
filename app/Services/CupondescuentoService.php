<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Cupondescuento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CupondescuentoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Cupondescuento::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Cupondescuento::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $cupondescuento = Cupondescuento::create($data);

        if (!$cupondescuento) {
            return response()->json(['error' => 'Failed to create Cupondescuento'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cupondescuento, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $cupondescuento = Cupondescuento::find($request->id);

        if (!$cupondescuento) {
            return response()->json(['error' => 'Cupondescuento not found'], Response::HTTP_NOT_FOUND);
        }

        $cupondescuento->update($request->all());
        $cupondescuento->refresh();

        return response()->json($cupondescuento, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cupondescuento = Cupondescuento::find($request->id);

        if (!$cupondescuento) {
            return response()->json(['error' => 'Cupondescuento not found'], Response::HTTP_NOT_FOUND);
        }

        $cupondescuento->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
