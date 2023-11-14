<?php

namespace App\Filters\Banners;

use App\Transformers\Banners\FindAllTransformer;
use Illuminate\Http\Response;

class BannersFilters
{
    public static function getPaginateBanners($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada usuario individualmente
        $bannersTransformados = $data->map(function ($banners) use ($transformer) {
            return $transformer->transform($banners);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $bannersTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
