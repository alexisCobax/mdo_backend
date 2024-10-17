<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
use App\Transformers\Productos\FindAllWebTransformer;

class ProductosWebFiltersRebajados
{
    public static function getPaginateProducts($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE', 1)); // Default page to 1
        $perPage = $request->input('cantidad', env('PER_PAGE', 15)); // Default items per page to 15

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        $query->join('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
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
            ->where('producto.precioPromocional', '>', 0)
            ->where('producto.precioPromocional', '<', 9.99)
            ->where('producto.precio', '>=', 'producto.precioPromocional')
            ->where('producto.stock', '>', 0)
            ->where('producto.suspendido', '=', 0)
            ->whereNull('producto.borrado')
            ->orderBy('marcaproducto.nombre', 'asc')
            ->orderBy('producto.ultimoIngresoDeMercaderia', 'desc')
            ->orderBy('producto.id', 'asc');

        // Pagina los resultados
        $data = $query->paginate($perPage, ['*'], 'pagina', $page); // 'pagina' como el nombre del parámetro de página

        // Crea una instancia del transformer
        $transformer = new FindAllWebTransformer();

        // Transforma cada producto individualmente
        $productosTransformados = $data->getCollection()->map(function ($producto) use ($transformer) {
            return $transformer->transform($producto);
        });

        // Crea la respuesta con datos paginados
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
