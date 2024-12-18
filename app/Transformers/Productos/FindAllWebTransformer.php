<?php

namespace App\Transformers\Productos;

use App\Models\Fotoproducto;
use App\Enums\EstadosProductosEnums;
use League\Fractal\TransformerAbstract;

class FindAllWebTransformer extends TransformerAbstract
{
    public function transform($producto)
    {
        $arrayEnum = EstadosProductosEnums::toArray();

        $imagenPrincipal = Fotoproducto::where('id',$producto->imagenPrincipal)->first();

        if(isset($imagenPrincipal->url)){
            $imagen = $imagenPrincipal->url;
        }else{
            $imagen = env('URL_IMAGENES_PRODUCTOS').$producto->imagenPrincipal . '.jpg';
        }

        return [
            'id' => $producto->producto_id,
            'imagenPrincipal' => $imagen,
            'nombre' => utf8_encode($producto->nombre),
            'codigo' => $producto->codigo,
            'categoria' => $producto->categoria,
            'categoriaNombre' => optional($producto->categorias)->nombre,
            'precio' => $producto->precioPromocional == 0 ? number_format($producto->precio, 2) : number_format($producto->precioPromocional, 2),
            'precioLista' => number_format($producto->precio, 2),
            'stock' => $producto->stock,
            'destacado' => $producto->destacado,
            'marca' => $producto->marca_id,
            'nombreMarca' => utf8_encode($producto->marca_nombre),
            'colorNombre' => $producto->color,
            'precioPromocional' => number_format($producto->precioPromocional, 2),
            'nuevo' => $producto->nuevo,
            'estado' => $producto->suspendido == $arrayEnum[EstadosProductosEnums::SUSPENDIDO] ? EstadosProductosEnums::SUSPENDIDO : EstadosProductosEnums::PUBLICADO,
        ];
    }
}
