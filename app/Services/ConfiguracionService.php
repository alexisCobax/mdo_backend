<?php

namespace App\Services;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class ConfiguracionService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Configuracion::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Configuracion::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $configuracion = Configuracion::create($data);

        if (!$configuracion) {
            return response()->json(['error' => 'Failed to create Configuracion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($configuracion, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $configuracion = Configuracion::find($request->id);

        if (!$configuracion) {
            return response()->json(['error' => 'Configuracion not found'], Response::HTTP_NOT_FOUND);
        }

        $configuracion->update($request->all());
        $configuracion->refresh();

        return response()->json($configuracion, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $configuracion = Configuracion::find($request->id);

        if (!$configuracion) {
            return response()->json(['error' => 'Configuracion not found'], Response::HTTP_NOT_FOUND);
        }

        $configuracion->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
