<?php

namespace App\Filters\Pedidos;

use App\Transformers\Pedidos\FindAllTransformer;
use Illuminate\Http\Response;

class PedidosFilters
{
    public static function getPaginatePedidos($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $nombreCliente = $request->input('nombreCliente');
        $id = $request->input('id');
        $estado = $request->input('estado');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->codigo($id);
        $query->nombreCliente($nombreCliente);
        $query->estado($estado);
        $query->stockRange($desde, $hasta);

        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada pedido individualmente
        $pedidosTransformados = $data->map(function ($pedido) use ($transformer) {
            return $transformer->transform($pedido);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $pedidosTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
