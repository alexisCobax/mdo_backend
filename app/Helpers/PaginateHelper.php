<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class PaginateHelper
{
    /**
     * getPaginatedData.
     *
     * @param  mixed $request
     * @param  mixed $model
     * @return void
     */
    public static function getPaginatedData($request, $model)
    {
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $data = $model::orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $data->items(),
        ];

        return response()->json($response);
    }
}
