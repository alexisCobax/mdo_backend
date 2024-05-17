<?php

namespace App\Filters\Marcas;

use App\Transformers\Marcas\FindAllTransformer;
use Illuminate\Http\Response;

class MarcasFilters
{
    public static function getPaginateMarcas($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $nombreCliente = $request->input('nombreCliente');
        $estado = $request->input('estado');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->where('suspendido', 0);

        $data = $query->orderBy('nombre', 'asc')
        ->paginate($perPage, ['*'], 'page', $page);

        // // Obtén el valor de la cantidad desde la URL (por ejemplo, 'cantidad=5')
        // $cantidad = request('cantidad');

        // // Verifica si se proporciona una cantidad válida en la URL
        // if ($cantidad && is_numeric($cantidad)) {
        //     $cantidad = max(1, $cantidad); // Si se proporciona una cantidad, úsala sin límites inferiores
        // }

        // // Ejecuta la consulta y obtén los resultados
        // if ($cantidad) {
        //     $data = $query->orderBy('id', 'desc')->take($cantidad)->get();
        // } else {
        //     $data = $query->orderBy('id', 'desc')->get(); // Si no se proporciona cantidad, obtén todos los datos.
        // }

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada usuario individualmente
        $marcasTransformadas = $data->map(function ($usuario) use ($transformer) {
            return $transformer->transform($usuario);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $marcasTransformadas,
        ];

        // Devuelve la respuesta
        return response()->json($response);

        // Devuelve la respuesta
        return response()->json($response);
    }
}
