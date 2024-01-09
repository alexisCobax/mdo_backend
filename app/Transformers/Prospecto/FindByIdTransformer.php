<?php

namespace App\Transformers\Cliente;

use App\Models\Cliente;
use App\Models\Usuario;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function transform()
    {
        return [
             Cliente::find(),
            'usuario' => Usuario::find(),
        ];
    }
}
