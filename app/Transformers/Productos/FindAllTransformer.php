<?php

namespace App\Transformers\Productos;

use App\Enums\EstadosProductosEnums;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($producto)
    {
        $arrayEnum = EstadosProductosEnums::toArray();

        return [
            'id' => $producto->id,
            'imagenPrincipal' => $producto->imagenPrincipal . '.' . env('EXTENSION_IMAGEN_PRODUCTO'),
            'nombre' => $producto->nombre,
            'codigo' => $producto->codigo,
            'categoria' => $producto->categoria,
            'categoriaNombre' => optional($producto->categorias)->nombre,
            'precio' => $producto->precioPromocional == 0 ? number_format($producto->precio, 2) : number_format($producto->precioPromocional, 2),
            'precioLista' => $producto->precio,
            'stock' => $producto->stock,
            'destacado' => $producto->destacado,
            'marca' => optional($producto->marcas)->id,
            'nombreMarca' => optional($producto->marcas)->nombre,
            'precioPromocional' => $producto->precioPromocional,
            'estado' => $producto->suspendido == $arrayEnum[EstadosProductosEnums::SUSPENDIDO] ? EstadosProductosEnums::SUSPENDIDO : EstadosProductosEnums::PUBLICADO,
        ];
    }
}
