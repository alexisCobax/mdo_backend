<?php

namespace App\Filters\Pedidos;

use App\Helpers\DateHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PedidosProductosFilters
{
    public static function getPaginatePedidos($request)
    {

        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $nombreCliente = trim($request->input('nombreCliente'));
        $id = $request->input('id');
        $estado = $request->input('estado');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $SQL = "
        SELECT
            pedido.id,
            pedido.fecha,
            pedido.cliente,
            pedido.estado,
            pedido.vendedor,
            pedido.formaDePago,
            cliente.nombre AS nombreCliente,
            pedido.total,
            estadopedido.nombre AS nombreEstado,
            encargadodeventa.nombre
        FROM pedido
        LEFT JOIN estadopedido ON estadopedido.id = pedido.estado
        LEFT JOIN encargadodeventa ON pedido.vendedor = encargadodeventa.id
        LEFT JOIN cliente ON pedido.cliente = cliente.id
        WHERE
            pedido.id IN(
            SELECT
                pedidodetalle.pedido
            FROM
                pedidodetalle
            LEFT JOIN producto ON producto.id = pedidodetalle.producto
            WHERE
                producto.codigo = {$request->codigoProducto}
        ) AND (cliente.nombre LIKE '%{$nombreCliente}%')
    ";

        if ($request->desde != '') {
            $SQL .= " AND (pedido.fecha>='{$desde}')";
        }

        if ($request->hasta != '') {
            $SQL .= " AND (pedido.fecha<='{$hasta}')";
        }

        if ($request->estado != 0) {
            $SQL .= " AND (pedido.estado<='{$estado}')";
        }

        if ($request->id != '') {
            $SQL .= " AND (pedido.id='{$id}')";
        }

        $data = DB::select($SQL);

        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($data, $offset, $perPage);
        $data = collect($pagedData);

        $pedidosTransformados = $data->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'fecha' => DateHelper::ToDateCustom($pedido->fecha),
                'nombreCliente' => $pedido->nombreCliente,
                'cliente' => $pedido->cliente,
                'nombreEmpleado' => $pedido->nombre,
                'nombreEstado' => $pedido->nombreEstado,
                'total' => $pedido->total,
            ];
        });

        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->count(),
            'cantidad_por_pagina' => $perPage,
            'pagina' => $page,
            'cantidad_total' => $data->count(),
            'results' => $pedidosTransformados,
        ];

        return response()->json($response);
    }
}
