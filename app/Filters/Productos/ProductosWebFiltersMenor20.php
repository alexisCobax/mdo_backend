<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
use App\Transformers\Productos\FindAllWebTransformer;
use Illuminate\Support\Facades\DB;

class ProductosWebFiltersMenor20
{
    public static function getPaginateProducts($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

$query->select(
            'producto.id as producto_id',
            DB::raw("CONCAT('https://phpstack-1091339-3819555.cloudwaysapps.com/storage/app/public/images/',
                COALESCE(
                    IF(producto.proveedorExterno='nywd',
                        IF(
                            fotoproducto.descargada = 2,
                            SUBSTRING_INDEX(fotoproducto.url, '/', -1),
                            '0.jpg'
                        ),
                        CONCAT(producto.imagenPrincipal, '.jpg')
                    ),
                    '0.jpg'
                )
            ) AS imagenPrincipal"),
            'producto.codigo',
            'producto.nombre',
            'producto.categoria',
            'categoriaproducto.id as categoria_id',
            'categoriaproducto.nombre as categoria_nombre',
            'producto.precio',
            'producto.stock',
            'producto.destacado',
            'producto.marca',
            'marcaproducto.nombre as nombreMarca',
            'producto.color as colorNombre',
            'producto.precioPromocional',
            'producto.nuevo',
            'producto.suspendido'
        )
        ->leftjoin('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
        ->leftjoin('categoriaproducto', 'producto.categoria', '=', 'categoriaproducto.id')
        ->leftJoin('fotoproducto', 'fotoproducto.id', '=', 'producto.imagenPrincipal')
        ->where(function($query) {
            $query->whereBetween('producto.precioPromocional', [10, 25])
                ->orWhereBetween('producto.precio', [10, 25]);
        })
        ->where('producto.stock', '>', 0)
        ->where('producto.suspendido', '=', 0)
        ->whereNull('producto.borrado')
        ->orderBy('marcaproducto.nombre', 'ASC')
        ->orderBy('producto.ultimoIngresoDeMercaderia', 'DESC')
        ->orderBy('producto.id', 'ASC');


        // Pagina los resultados
        $data = $query->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllWebTransformer();

        // Transforma cada producto individualmente
        $productosTransformados = $data->map(function ($producto) use ($transformer) {
            return $transformer->transform($producto);
        });

        // Prepara la respuesta
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $productosTransformados,
        ];

        return response()->json($response);
    }
}
