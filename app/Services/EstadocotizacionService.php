<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Estadocotizacion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EstadocotizacionService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Estadocotizacion::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Estadocotizacion::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $estadocotizacion = Estadocotizacion::create($data);

        if (!$estadocotizacion) {
            return response()->json(['error' => 'Failed to create Estadocotizacion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($estadocotizacion, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $estadocotizacion = Estadocotizacion::find($request->id);

        if (!$estadocotizacion) {
            return response()->json(['error' => 'Estadocotizacion not found'], Response::HTTP_NOT_FOUND);
        }

        $estadocotizacion->update($request->all());
        $estadocotizacion->refresh();

        return response()->json($estadocotizacion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $estadocotizacion = Estadocotizacion::find($request->id);

        if (!$estadocotizacion) {
            return response()->json(['error' => 'Estadocotizacion not found'], Response::HTTP_NOT_FOUND);
        }

        $estadocotizacion->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
