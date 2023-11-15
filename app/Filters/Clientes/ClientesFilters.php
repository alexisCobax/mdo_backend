<?php

namespace App\Filters\Clientes;

use App\Transformers\Cliente\FindAllTransformer;
use Illuminate\Http\Response;

class ClientesFilters
{
    public static function getPaginateClientes($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $id = $request->input('id');
        $nombre = $request->input('nombre');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        if ($id) {
            $query->id($id);
        }
        if ($nombre) {
            $query->nombre($nombre);
        }

        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada usuario individualmente
        $clientesTransformados = $data->map(function ($clientes) use ($transformer) {
            return $transformer->transform($clientes);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $clientesTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
