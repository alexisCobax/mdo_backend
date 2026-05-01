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
        $permisos = $request->input('permisos');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Filtro por permisos (ej. permisos=2,3): solo usuarios con esos permisos
        if ($permisos !== null && $permisos !== '') {
            $ids = array_map('intval', array_filter(explode(',', $permisos)));
            if (!empty($ids)) {
                $query->whereIn('permisos', $ids);
            }
        } else {
            // Aplica filtro por perfil solo si no se envió permisos
            $query->perfil($perfil);
        }

        // Filtro por perfil desde el formulario (selector de perfiles): restringe a un perfil
        if ($perfil !== null && $perfil !== '' && is_numeric($perfil) && (int) $perfil > 0) {
            $query->where('permisos', (int) $perfil);
        }

        // Eager load permiso para el nombre del perfil (evita N+1)
        $data = $query->with('permiso')
            ->orderBy('id', 'desc')
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
