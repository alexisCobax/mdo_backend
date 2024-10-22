<?php

namespace App\Transformers\Productos;

use App\Models\Fotoproducto;
use App\Models\Producto;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public function transform(Producto $producto)
    {

        $fotos = optional($producto->fotos);

        $imagenes = [];

        if ($fotos) {
            foreach ($producto->fotos as $foto) {

                $imagen = '';

                // if(isset($foto->url) && $foto->url==''){
                //     $imagen = env('URL_IMAGENES_PRODUCTOS') . $foto->id . '.jpg';
                // }else{
                //     $imagen = $foto->url;
                // }

                if(isset($foto->url)){
                    $imagen = $foto->url;
                }else{
                    $imagen = env('URL_IMAGENES_PRODUCTOS').$foto->id . '.jpg';
                }

                $imagenes[] = [
                    'id' => $foto->id,
                    'url' => $imagen,
                    'orden' => $foto->orden,
                ];
            }
        }

        if($producto->imagenPrincipal!=0){
            $imagen = Fotoproducto::where('id',$producto->imagenPrincipal)->first();

            if(isset($imagen->url) && $imagen->url==NULL){
                $urlImagen = env('URL_IMAGENES_PRODUCTOS') . $imagen->id . '.jpg';
            }else{
                $urlImagen = $imagen->url;
            }
        }else{
            $urlImagen = env('URL_IMAGENES_PRODUCTOS').'0.jpg';
        }


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
            //'nombreMaterial' => optional($producto->materiales)->nombre,
            'nombreMaterial' => $producto->material,
            'estuche' => $producto->estuche,
            'estucheWeb' => $producto->estuche == 0 ? 'No' : ($producto->estuche == 1 ? 'Si' : 'Si, Genérico'),
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
            'imagenPrincipal' => $urlImagen,
            //'imagenesSecundarias' => optional($producto->fotos)->orden . '.' . env('EXTENSION_IMAGEN_PRODUCTO'),
            'UPCreal' => $producto->UPCreal,
            'mdoNet' => $producto->mdoNet,
            'jet' => $producto->jet,
            'categoriaJet' => $producto->jet,
            'peso' => $producto->peso,
            'verEnFalabella' => $producto->verEnFalabella,
            'categoriaFalabella' => $producto->categoriaFalabella,
            'precioPromocional' => $producto->precioPromocional,
            'nuevo' => $producto->nuevo,
            'precioLista' => $producto->precio,
            'destacado' => $producto->destacado,
            'largo' => $producto->largo,
            'alto' => $producto->alto,
            'ancho' => $producto->ancho,
            'descripcionLarga' => $producto->descripcionLarga,
            'imagenes' => $imagenes,
        ];
    }
}
