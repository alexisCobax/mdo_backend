<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
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


        $query->where(function ($query) {
            $query->where('precioPromocional', '>', 0)
                  ->orWhere(function ($query) {
                      $query->where('precioPromocional', '<=', 9.99)
                            ->orWhereBetween('precio', [0, 9.99]);
                  });
        });

        $query->join('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
            ->select(
                'producto.id as producto_id',
                'producto.nombre',
                'producto.codigo',
                'producto.categoria',
                'producto.precio',
                'producto.precioPromocional',
                'producto.stock',
                'producto.destacado',
                'producto.color',
                'producto.nuevo',
                'producto.imagenPrincipal',
                'marcaproducto.nombre as marca_nombre',
                'marcaproducto.id as marca_id'
            )
            ->where('producto.stock', '>', 0)
            ->where('producto.suspendido', '=', 0)
            ->whereNull('borrado')
            ->orderBy('marcaproducto.nombre', 'asc')
            ->orderBy('producto.ultimoIngresoDeMercaderia', 'desc')
            ->orderBy('producto.id', 'asc');

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
