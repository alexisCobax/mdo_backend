<?php

namespace App\Services;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PerfilService
{
    /**
     * Lista todos los perfiles/permisos (tabla permiso) para filtros y selectores.
     * El frontend espera res.data.results con array de { id, nombre }.
     */
    public function findAll(Request $request)
    {
        try {
            $results = Permiso::orderBy('id')->get(['id', 'nombre']);

            // El frontend espera res.data.results (axios: res.data = cuerpo JSON)
            return response()->json(['results' => $results], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al obtener los perfiles',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Permiso::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $permiso = Permiso::create($data);

        if (!$permiso) {
            return response()->json(['error' => 'Failed to create Permiso'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($permiso, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $permiso = Permiso::find($request->id);

        if (!$permiso) {
            return response()->json(['error' => 'Permiso not found'], Response::HTTP_NOT_FOUND);
        }

        $permiso->update($request->all());
        $permiso->refresh();

        return response()->json($permiso, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $permiso = Permiso::find($request->id);

        if (!$permiso) {
            return response()->json(['error' => 'Permiso not found'], Response::HTTP_NOT_FOUND);
        }

        $permiso->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
