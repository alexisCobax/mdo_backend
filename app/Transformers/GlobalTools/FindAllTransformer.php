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
            'tiposProducto' => Tipoproducto::orderBy('nombre', 'asc')->get()->toArray(),
            'colores' => Color::orderBy('nombre', 'asc')->get()->toArray(),
            'categoria' => Categoriaproducto::orderBy('nombre', 'asc')->get()->toArray(),
            'materiales' => Materialproducto::orderBy('nombre', 'asc')->get()->toArray(),
            'tamanios' => Tamanoproducto::orderBy('nombre', 'asc')->get()->toArray(),
            'estuche' => Estuche::all()->toArray(),
            'grupo' => Grupo::all()->toArray(),
            'genero' => Productogenero::all()->toArray(),
        ];
    }
}
