<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Helpers\PaginateHelper;
use Illuminate\Http\Response;

class UsuarioService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Usuario::class);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Usuario::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $existe = Usuario::where('nombre', $request->usuario)->count();
        if($existe!=0) {
            return response()->json(['error' => 'Usuario existente', 'code' => 401], Response::HTTP_UNAUTHORIZED);
        }

        $data = $request->all();
        $usuario = Usuario::create($data);

        if (!$usuario) {
            return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($usuario, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $usuario = Usuario::find($request->id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario not found'], Response::HTTP_NOT_FOUND);
        }

        $usuario->update($request->all());
        $usuario->refresh();

        return response()->json($usuario, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $usuario = Usuario::find($request->id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario not found'], Response::HTTP_NOT_FOUND);
        }

        $usuario->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

}
