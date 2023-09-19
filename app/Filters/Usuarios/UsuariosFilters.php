<?php

namespace App\Filters\Usuarios;

use App\Transformers\Usuarios\FindAllTransformer;
use Illuminate\Http\Response;

class UsuariosFilters
{
    public static function getPaginateUsuarios($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $perfil = $request->input('perfil');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->perfil($perfil);
 
        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada usuario individualmente
        $usuariosTransformados = $data->map(function ($usuario) use ($transformer) {
            return $transformer->transform($usuario);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $usuariosTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
