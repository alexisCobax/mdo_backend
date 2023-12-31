<?php

namespace App\Transformers\Productos;

use App\Models\Producto;
use League\Fractal\TransformerAbstract;

class CreateBulkTransformer extends TransformerAbstract
{
    public function transform(Producto $producto)
    {

        return [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'tipo' => $producto->tipo,
            'categoria' => $producto->categoria,
            'nombreCategoria' => optional($producto->categorias)->nombre,
            'marca' => $producto->marca,
            'nombreMarca' => optional($producto->marcas)->nombre,
            'material' => $producto->material,
            'nombreMaterial' => optional($producto->materiales)->nombre,
            'estuche' => $producto->estuche,
            'proveedor' => $producto->proveedor,
            'precio' => $producto->precioPromocional == 0 ? $producto->precio : $producto->precioPromocional,
            'suspendido' => $producto->suspendido,
            'comision' => $producto->comision,
            'stock' => $producto->stock,
            'stockMinimo' => $producto->stockMinimo,
            'codigo' => $producto->codigo,
            'alarmaStockMinimo' => $producto->alarmaStockMinimo,
            'color' => $producto->color,
            'nombreColor' => optional($producto->colores)->nombre,
            'colorPrincipal' => $producto->colorPrincipal,
            'colorSecundario' => $producto->colorSecundario,
            'tamano' => $producto->tamano,
            'ubicacion' => $producto->ubicacion,
            'grupo' => $producto->grupo,
            'pagina' => $producto->pagina,
            'costo' => $producto->costo,
            'posicion' => $producto->posicion,
            'stockRoto' => $producto->stockRoto,
            'genero' => $producto->genero,
            // 'imagenPrincipal' => $producto->imagenPrincipal . '.' . env('EXTENSION_IMAGEN_PRODUCTO'),
            'UPCreal' => $producto->UPCreal,
            'mdoNet' => $producto->mdoNet,
            'jet' => $producto->jet,
            'categoriaJet' => $producto->jet,
            'peso' => $producto->peso,
            'verEnFalabella' => $producto->verEnFalabella,
            'categoriaFalabella' => $producto->categoriaFalabella,
            'precioPromocional' => $producto->precioPromocional,
            'precioLista' => $producto->precio,
            'destacado' => $producto->destacado,
            'largo' => $producto->largo,
            'alto' => $producto->alto,
            'ancho' => $producto->ancho,
            'descripcionLarga' => $producto->descripcionLarga,
        ];
    }
}
