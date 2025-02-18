<?php

namespace App\Filters\Cotizaciones;

use App\Transformers\Cotizacion\FindAllTransformer;
use Illuminate\Http\Response;

class CotizacionesFilters
{
    public static function getPaginateCotizaciones($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $nombreCliente = $request->input('nombreCliente');
        $id = $request->input('id');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();
        $query->where('id', '>', 4087);
        $query->clienteFiltro($nombreCliente);
        $query->id($id);
        $query->desdeHasta($desde,$hasta);



        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada pedido individualmente
        $comprasTransformadas = $data->map(function ($compra) use ($transformer) {
            return $transformer->transform($compra);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $comprasTransformadas,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
