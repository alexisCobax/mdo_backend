<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Transformers\Productos\FindAllTransformer;
use App\Transformers\Productos\FindAllWebTransformer;

class ProductosWebFiltersRebajados
{
    public static function getPaginateProducts($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

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
            ->where(function ($query) {
                $query->where('producto.precioPromocional', '>', 0)
                    ->where('producto.precioPromocional', '<', 9.99)
                    ->where('producto.precio', '>=', DB::raw('producto.precioPromocional'));
            })
            ->where('producto.stock', '>', 0)
            ->where('producto.suspendido', '=', 0)
            ->whereNull('producto.borrado')
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
