<?php

namespace App\Transformers\GlobalTools;

use App\Models\Marcaproducto;
use App\Models\Tipoproducto;
use App\Models\Color;
use App\Models\Categoriaproducto;
use App\Models\Materialproducto;
use App\Models\Tamanoproducto;
use App\Models\Estuche;
use App\Models\Grupo;
use App\Models\Productogenero;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform()
    {
        return [
            'marcas' => Marcaproducto::all()->toArray(),
            'tiposProducto' => Tipoproducto::all()->toArray(),
            'colores' => Color::all()->toArray(),
            'categoria' => Categoriaproducto::all()->toArray(),
            'materiales' => Materialproducto::all()->toArray(),
            'tamanios' => Tamanoproducto::all()->toArray(),
            'estuche' => Estuche::all()->toArray(),
            'grupo' => Grupo::all()->toArray(),
            'genero' => Productogenero::all()->toArray()
        ];
    }
}
