<?php

namespace App\Transformers\Usuarios;

use App\Helpers\DateHelper;
use App\Models\Usuario;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Usuario $usuario)
    {
        $compra = [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'perfil' => 'Usuario',
            'estado' => $usuario->suspendido
        ];

        return $compra;
    }
}
