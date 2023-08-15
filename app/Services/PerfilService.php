<?php

namespace App\Services;

use App\Models\Perfil;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class PerfilService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Perfil::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Perfil::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $perfil = Perfil::create($data);

        if (!$perfil) {
            return response()->json(['error' => 'Failed to create Perfil'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($perfil, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $perfil = Perfil::find($request->id);

        if (!$perfil) {
            return response()->json(['error' => 'Perfil not found'], Response::HTTP_NOT_FOUND);
        }

        $perfil->update($request->all());
        $perfil->refresh();

        return response()->json($perfil, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $perfil = Perfil::find($request->id);

        if (!$perfil) {
            return response()->json(['error' => 'Perfil not found'], Response::HTTP_NOT_FOUND);
        }

        $perfil->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
