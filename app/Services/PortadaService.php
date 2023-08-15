<?php

namespace App\Services;

use App\Models\Portada;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PortadaService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Portada::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Portada::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $portada = Portada::create($data);

        if (!$portada) {
            return response()->json(['error' => 'Failed to create Portada'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($portada, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $portada = Portada::find($request->id);

        if (!$portada) {
            return response()->json(['error' => 'Portada not found'], Response::HTTP_NOT_FOUND);
        }

        $portada->update($request->all());
        $portada->refresh();

        return response()->json($portada, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $portada = Portada::find($request->id);

        if (!$portada) {
            return response()->json(['error' => 'Portada not found'], Response::HTTP_NOT_FOUND);
        }

        $portada->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
