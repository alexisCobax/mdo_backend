<?php

namespace App\Filters\Recibos;

use App\Helpers\DateHelper;
use Illuminate\Http\Response;

class RecibosFilters
{
    public static function getPaginateRecibo($request, $model)
    {

        $recibosTransformados = [];

        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        foreach ($data->items() as $item) {
            $recibosTransformados[] = [
                'id'=> $item->id,
                'cliente'=> $item->cliente,
                'clienteNombre'=> optional($item->clientes)->nombre,
                'fecha'=> DateHelper::ToDateCustom($item->fecha),
                'formaDePago'=> $item->formaDePago,
                'total'=> $item->total,
                'anulado'=> $item->anulado,
                'observaciones'=> $item->observaciones,
                'pedido'=> $item->pedido,
                'garantia'=> $item->garantia,
            ];
        }

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $recibosTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
