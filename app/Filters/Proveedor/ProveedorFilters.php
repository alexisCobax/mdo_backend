<?php

namespace App\Filters\Proveedor;

use App\Transformers\Proveedores\FindAllTransformer;
use Illuminate\Http\Response;

class ProveedorFilters
{
    public static function getPaginateProveedor($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $nombre = $request->input('nombre');
        $contacto = $request->input('contacto');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        if ($nombre != 'undefined') {
            $query->nombre($nombre);
        }

        if ($contacto != 'undefined') {
            $query->contacto($contacto);
        }


        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada pedido individualmente
        $proveedoresTransformados = $data->map(function ($pedido) use ($transformer) {
            return $transformer->transform($pedido);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $proveedoresTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
