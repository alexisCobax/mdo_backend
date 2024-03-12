<?php

namespace App\Filters\Compras;

use App\Models\Compra;
use Illuminate\Http\Response;
use App\Transformers\Compra\FindAllCodigoTransformer;

class ComprasProductoFilters
{
    public static function getPaginateCompras($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $deposito = $request->input('deposito');
        $proveedor = $request->input('nombreProveedor');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $codigo = $request->input('codigo');

            // Inicializa la consulta utilizando el modelo
            $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->enDeposito($deposito);
        $query->proveedor($proveedor);
        $query->desdeHasta($desde, $hasta);

        // Realizar la consulta
        $query = Compra::query();
        $query->select(
            'compra.id',
            'compra.proveedor',
            'compra.observaciones',
            'compra.pagado',
            'compra.enDeposito',
            'compra.numeroLote',
            'compra.precio',
            'proveedor.nombre as proveedor',
            'proveedor.id as idProveedor'
        )
            ->distinct()
            ->join('compradetalle', 'compra.id', '=', 'compradetalle.compra')
            ->join('producto', 'compradetalle.producto', '=', 'producto.id')
            ->join('proveedor', 'compra.proveedor', '=', 'proveedor.id')
            ->where('producto.codigo', '=', $codigo);

        // Realiza la paginación de la consulta
        $data = $query->orderBy('compra.id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllCodigoTransformer();

        // Transforma cada pedido individualmente
        $comprasTransformadas = $data->map(function ($compra) use ($transformer) {
            return $transformer->transform($compra);
        });

        // Crea la respuesta personalizada
        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $comprasTransformadas,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
