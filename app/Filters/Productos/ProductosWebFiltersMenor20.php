<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
use App\Transformers\Productos\FindAllWebTransformer;

class ProductosWebFiltersMenor20
{
    public static function getPaginateProducts($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Inicializa la consulta utilizando el modelo
        $query = $model::query()
            ->join('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
            ->join('categoriaproducto', 'producto.categoria', '=', 'categoriaproducto.id')
            ->select(
                'producto.id as producto_id',
                'producto.imagenPrincipal',
                'producto.codigo',
                'producto.nombre',
                'producto.categoria',
                'categoriaproducto.id as categoria_id',
                'categoriaproducto.nombre as categoria_nombre',
                'producto.precio',
                'producto.stock',
                'producto.destacado',
                'producto.marca',
                'marcaproducto.nombre as marca_nombre',
                'producto.color as color_nombre',
                'producto.precioPromocional',
                'producto.nuevo',
                'producto.suspendido'
            )
            ->where('producto.stock', '>', 0)
            ->where('producto.suspendido', '=', 0)
            ->whereNull('producto.borrado')
            ->where(function ($query) {
                $query->whereBetween('producto.precioPromocional', [10, 25])
                      ->orWhereBetween('producto.precio', [10, 25]);
            })
            ->orderBy('marcaproducto.nombre', 'asc')
            ->orderBy('producto.ultimoIngresoDeMercaderia', 'desc')
            ->orderBy('producto.id', 'asc');

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
