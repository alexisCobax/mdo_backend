<?php

namespace App\Transformers\Marcas;

use App\Models\Marcaproducto;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Marcaproducto $marca)
    {
        $compra = [
            'id' => $marca->id,
            'nombre' => $marca->nombre
        ];

        return $compra;
    }
}
