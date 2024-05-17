<?php

namespace App\Transformers\GlobalTools;

use App\Models\Categoriaproducto;
use App\Models\Color;
use App\Models\Estuche;
use App\Models\Grupo;
use App\Models\Marcaproducto;
use App\Models\Materialproducto;
use App\Models\Productogenero;
use App\Models\Tamanoproducto;
use App\Models\Tipoproducto;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform()
    {
        return [
            'marcas' => Marcaproducto::where('suspendido', 0)->orderby('nombre','asc')->get()->toArray(),
            'tiposProducto' => Tipoproducto::all()->orderby('nombre','asc')->toArray(),
            'colores' => Color::all()->orderby('nombre','asc')->toArray(),
            'categoria' => Categoriaproducto::all()->orderby('nombre','asc')->toArray(),
            'materiales' => Materialproducto::all()->orderby('nombre','asc')->toArray(),
            'tamanios' => Tamanoproducto::all()->orderby('nombre','asc')->toArray(),
            'estuche' => Estuche::all()->toArray(),
            'grupo' => Grupo::all()->toArray(),
            'genero' => Productogenero::all()->toArray(),
        ];
    }
}
