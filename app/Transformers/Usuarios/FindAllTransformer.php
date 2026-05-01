<?php

namespace App\Transformers\Usuarios;

use App\Models\Usuario;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Usuario $usuario)
    {
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'perfil' => $usuario->permiso ? $usuario->permiso->nombre : (string) $usuario->permisos,
            'estado' => $usuario->suspendido,
        ];
    }
}
