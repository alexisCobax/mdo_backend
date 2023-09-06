<?php

namespace App\Services;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GrupoService
{
    public function findAll(Request $request)
    {
        try {
            $data = Grupo::all();

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Grupo::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $grupo = Grupo::create($data);

        if (!$grupo) {
            return response()->json(['error' => 'Failed to create Grupo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($grupo, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $grupo = Grupo::find($request->id);

        if (!$grupo) {
            return response()->json(['error' => 'Grupo not found'], Response::HTTP_NOT_FOUND);
        }

        $grupo->update($request->all());
        $grupo->refresh();

        return response()->json($grupo, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $grupo = Grupo::find($request->id);

        if (!$grupo) {
            return response()->json(['error' => 'Grupo not found'], Response::HTTP_NOT_FOUND);
        }

        $grupo->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
