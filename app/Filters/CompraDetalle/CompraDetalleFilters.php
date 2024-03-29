<?php

namespace App\Filters\CompraDetalle;

use App\Transformers\CompraDetalle\FindAllTransformer;
use Illuminate\Http\Response;

class CompraDetalleFilters
{
    public static function getPaginateCompraDetalle($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $deposito = $request->input('deposito');
        $compra = $request->input('compra');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->enDeposito($deposito);

        $query->compra($compra);

        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')->get();
        // ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada pedido individualmente
        $comprasTransformadas = $data->map(function ($compra) use ($transformer) {
            return $transformer->transform($compra);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            // 'total' => $data->total(),
            // 'cantidad_por_pagina' => $data->perPage(),
            // 'pagina' => $data->currentPage(),
            // 'cantidad_total' => $data->total(),
            'results' => $comprasTransformadas,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
