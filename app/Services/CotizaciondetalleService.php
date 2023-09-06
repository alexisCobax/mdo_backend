<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Cotizaciondetalle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CotizaciondetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Cotizaciondetalle::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Cotizaciondetalle::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $cotizaciondetalle = Cotizaciondetalle::create($data);

        if (!$cotizaciondetalle) {
            return response()->json(['error' => 'Failed to create Cotizaciondetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cotizaciondetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $cotizaciondetalle = Cotizaciondetalle::find($request->id);

        if (!$cotizaciondetalle) {
            return response()->json(['error' => 'Cotizaciondetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $cotizaciondetalle->update($request->all());
        $cotizaciondetalle->refresh();

        return response()->json($cotizaciondetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cotizaciondetalle = Cotizaciondetalle::find($request->id);

        if (!$cotizaciondetalle) {
            return response()->json(['error' => 'Cotizaciondetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $cotizaciondetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
