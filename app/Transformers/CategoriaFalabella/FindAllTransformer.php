<?php

namespace App\Transformers\CategoriaFalabella;

use App\Models\Categoriafalabella;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform()
    {
        $response = collect([]);

        $response = Categoriafalabella::all()->map(function ($categoria) {
            $categoriasFalabella = [
                'id' => $categoria->CategoryId,
                'name' => $categoria->Name,
                'hasChild' => true,
                'expanded' => false
            ];

            if ($categoria->PadreCategoryId != 0) {
                $categoriasFalabella['pid'] = $categoria->PadreCategoryId;
            }

            return $categoriasFalabella;
        });

        return $response;
    }
}
