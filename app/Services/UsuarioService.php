<?php

namespace App\Services;

use App\Filters\Usuarios\UsuariosFilters;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function findAll(Request $request)
    {
        try {
            $data = UsuariosFilters::getPaginateUsuarios($request, Usuario::class);

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
        $existe = Usuario::where('nombre', $request->nombre)->count();
        if ($existe != 0) {
            return response()->json(['error' => 'Usuario existente', 'code' => 401], Response::HTTP_UNAUTHORIZED);
        }
    
        $data = $request->all();
    
        // 🔐 Hashear la clave
        if (isset($data['clave'])) {
            $data['clave'] = Hash::make($data['clave']);
        }
    
        $usuario = Usuario::create($data);
    
        if (!$usuario) {
            return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return response()->json($usuario, Response::HTTP_OK);
    }
    
    // public function create(Request $request)
    // {
    //     $existe = Usuario::where('nombre', $request->usuario)->count();
    //     if ($existe != 0) {
    //         return response()->json(['error' => 'Usuario existente', 'code' => 401], Response::HTTP_UNAUTHORIZED);
    //     }

    //     $data = $request->all();
    //     $usuario = Usuario::create($data);

    //     if (!$usuario) {
    //         return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     return response()->json($usuario, Response::HTTP_OK);
    // }

    public function update(Request $request)
    {
        $usuario = Usuario::find($request->id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = $request->all();
        
        // 👉 Solo si viene la clave, la hasheás
        if (isset($data['clave'])) {
            $data['clave'] = Hash::make($data['clave']);
        }
        
        $usuario->update($data);
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
