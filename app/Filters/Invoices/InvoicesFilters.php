<?php

namespace App\Filters\Invoices;

use App\Transformers\Invoices\FindAllTransformer;
use Illuminate\Http\Response;

class InvoicesFilters
{
    public static function getPaginateInvoices($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $nombreCliente = $request->input('nombreCliente');
        $codigo = $request->input('id');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        $query->clienteFiltro($nombreCliente);
        $query->codigo($codigo);
        $query->desdeHasta($desde,$hasta);
        // $query->categoria($categoria);
        // $query->nombre($nombre);
        // $query->nombreMarca($nombreMarca);
        // $query->suspendido($suspendido);
        // $query->precioRange($precioDesde, $precioHasta);
        // $query->stockRange($stockDesde, $stockHasta);
        // $query->tipo($tipo);
        // $query->idMarca($idMarca);
        // $query->material($material);
        // $query->color($color);
        // $query->destacado($destacado);
        // $query->grupo($grupo);
        // $query->buscador($buscador);
        // $query->NuevosProductos($estado);

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
