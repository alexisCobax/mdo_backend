<?php

namespace App\Filters\Marcas;

use Illuminate\Http\Response;
use App\Transformers\Marcas\FindAllTransformer;

class MarcasFilters
{
    public static function getPaginateMarcas($model)
    {
        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->where('MostrarEnWeb', 1);

        // Obtén el valor de la cantidad desde la URL (por ejemplo, 'cantidad=5')
        $cantidad = request('cantidad');

        // Verifica si se proporciona una cantidad válida en la URL
        if ($cantidad && is_numeric($cantidad)) {
            $cantidad = max(1, $cantidad); // Si se proporciona una cantidad, úsala sin límites inferiores
        }

        // Ejecuta la consulta y obtén los resultados
        if ($cantidad) {
            $data = $query->orderBy('id', 'desc')->take($cantidad)->get();
        } else {
            $data = $query->orderBy('id', 'desc')->get(); // Si no se proporciona cantidad, obtén todos los datos.
        }

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada usuario individualmente
        $marcasTransformadas = $data->map(function ($usuario) use ($transformer) {
            return $transformer->transform($usuario);
        });

        // // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'results' => $marcasTransformadas,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
