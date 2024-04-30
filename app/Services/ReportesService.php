<?php

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ArrayToXlsxHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\PaginateHelper;

class ReportesService
{

    public function stockList(Request $request)
    {

        $query = DB::table('producto')
            ->select(
                'id AS idProducto',
                'codigo',
                'nombre AS nombreProducto',
                'color',
                'stock',
                'costo',
                'precio',
                DB::raw('stock * costo AS CostoStock'),
                DB::raw('stock * precio AS PrecioStock')
            )
            ->where('stock', '>', 0);

        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $data = $query->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);

        $results = $data->items();

        // Mapea los resultados para ajustar los nombres de las claves y eliminar las que no necesitas
        $transformedResults = array_map(function ($result) {
            return [
                'id' => $result->idProducto,
                'nombre' => $result->nombreProducto,
                'codigo' => $result->codigo,
                'precio' => $result->precio,
                'costo' => $result->costo,
                'stock' => $result->stock,
                'color' => $result->color,
            ];
        }, $results);

        // Construye la respuesta final
        $response = [
            'data' => [
                'headers' => [], // Puedes agregar encabezados si lo necesitas
                'original' => [
                    'status' => Response::HTTP_OK,
                    'total' => $data->total(),
                    'cantidad_por_pagina' => $data->perPage(),
                    'pagina' => $data->currentPage(),
                    'cantidad_total' => $data->total(),
                    'results' => $transformedResults,
                ],
                'exception' => null,
            ],
        ];

        return response()->json($response);
    }

    public function stockReport(Request $request)
    {
        try {
            $sql = "SELECT
        id AS idProducto,
        codigo,
        nombre AS nombreProducto,
        color,
        stock,
        costo,
        precio,
        stock * costo AS CostoStock,
        stock * precio AS PrecioStock
    FROM
        producto
    WHERE
        stock > 0";

            $stock = DB::select($sql);

            // Convertir los objetos stdClass en arrays asociativos
            $stock = array_map(function ($item) {
                return (array) $item;
            }, $stock);

            $cabeceras = ['idProducto', 'codigo', 'nombreProducto', 'color', 'stock', 'costo', 'precio', 'CostoStock', 'PrecioStock'];

            $response = ArrayToXlsxHelper::getXlsx($stock, $cabeceras);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function productosList(Request $request)
    {

        $fecha_inicio = '2024-01-01';
        $fecha_fin = '2024-02-01';

        $perPage = $request->input('cantidad', env('PER_PAGE'));
        $page = $request->input('pagina', env('PAGE'));

        $query = DB::table('pedidodetalle')
            ->select(
                DB::raw('SUM(pedidodetalle.cantidad) AS cantidad'),
                'producto.id',
                'producto.color',
                'producto.nombre',
                'producto.stock',
                'producto.codigo',
                'producto.id AS idProducto',
                'producto.precio',
                'producto.costo',
                DB::raw('((producto.precio - producto.costo) * SUM(pedidodetalle.cantidad)) AS ganancia'),
                DB::raw('(producto.stock * producto.costo) AS CostoStock'),
                DB::raw('SUM(pedidodetalle.cantidad) * producto.precio AS total'),
                DB::raw('(producto.costo * SUM(pedidodetalle.cantidad)) AS costoVenta')
            )
            ->leftJoin('producto', 'pedidodetalle.producto', '=', 'producto.id')
            ->leftJoin('color', 'producto.color', '=', 'color.id')
            ->leftJoin('pedido', 'pedidodetalle.pedido', '=', 'pedido.id')
            ->whereBetween('pedido.fecha', [$fecha_inicio, $fecha_fin])
            ->where('pedido.estado', '<>', 4)
            ->groupBy('producto.color', 'color.nombre', 'producto.nombre', 'producto.stock', 'producto.id', 'producto.precio', 'producto.costo')
            ->orderBy('producto.nombre', 'asc');

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        $transformedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'cantidad' => $result->cantidad,
                'codigo' => $result->codigo,
                'color' => $result->color,
                'nombre' => $result->nombre,
                'stock' => $result->stock,
                'idProducto' => $result->idProducto,
                'precio' => $result->precio,
                'costo' => $result->costo,
                'ganancia' => $result->ganancia,
                'CostoStock' => $result->CostoStock,
                'total' => $result->total,
                'costoVenta' => $result->costoVenta
            ];
        });

        $response = [
            'data' => [
                'headers' => [],
                'original' => [
                    'status' => Response::HTTP_OK,
                    'total' => $results->total(),
                    'cantidad_por_pagina' => $results->perPage(),
                    'pagina' => $results->currentPage(),
                    'cantidad_total' => $results->total(),
                    'results' => $transformedResults,
                ],
                'exception' => null,
            ],
        ];

        return response()->json($response);
    }

    public function productosReport(Request $request)
    {

        try {

            $fecha_inicio = '2024-01-01';
            $fecha_fin = '2024-02-01';

            $productos = DB::select("
            SELECT SUM(pedidodetalle.cantidad) AS cantidad,
            producto.color,
            producto.nombre,
            producto.stock,
            producto.id AS idProducto,
            producto.precio,
            producto.costo,
            ((producto.precio - producto.costo) * SUM(pedidodetalle.cantidad)) as ganancia,
            (producto.stock * producto.costo) as CostoStock,
            SUM(pedidodetalle.cantidad) * producto.precio as total,
            ( producto.costo * SUM(pedidodetalle.cantidad)) as costoVenta
   FROM
    pedidodetalle
       LEFT JOIN producto  on pedidodetalle.producto = producto.id
       LEFT JOIN color ON producto.color = color.id
       LEFT JOIN pedido ON pedidodetalle.pedido = pedido.id
   WHERE pedido.fecha BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}' AND pedido.estado <> 4
       GROUP BY producto.color, color.nombre, producto.nombre, producto.stock, pedidodetalle.precio, producto.id, producto.precio, producto.costo
UNION
   SELECT pedidodetallenn.cantidad AS cantidad,
           '' AS nombreColor,
           pedidodetallenn.descripcion, '' as stock,
           '' AS idProducto,
           pedidodetallenn.precio,
           0 as costo, 0 as ganancia,
           0 as CostoStock,
           pedidodetallenn.cantidad * pedidodetallenn.precio as total,
           0 as costoVenta
    FROM
       pedidodetallenn
       LEFT JOIN pedido ON pedidodetallenn.pedido = pedido.id
    WHERE pedido.fecha BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}' AND pedido.estado <> 4
    order by 3
", [$fecha_inicio, $fecha_fin]);



            // Convertir los objetos stdClass en arrays asociativos
            $productos = array_map(function ($item) {
                return (array) $item;
            }, $productos);

            $cabeceras = ['id', 'cantidad', 'color', 'nombre', 'stock', 'id producto', 'precio', 'costo', 'ganancia', 'costo stock', 'total', 'costo venta'];

            $response = ArrayToXlsxHelper::getXlsx($productos, $cabeceras);

            return $response;


            //     return response()->json(['data' => $productos], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function invoicesList(Request $request){

        $perPage = request()->input('cantidad', env('PER_PAGE'));
        $page = request()->input('pagina', env('PAGE'));

        $query = DB::table('invoice')
            ->select(
                'invoice.id AS id',
                DB::raw('DATE(invoice.fecha) AS fecha'),
                'invoice.cliente',
                'invoice.total',
                'invoice.formaDePago',
                'invoice.estado',
                'invoice.observaciones',
                'invoice.anulada',
                'invoice.billTo',
                'invoice.shipTo',
                'invoice.shipVia',
                'invoice.FOB',
                'invoice.Terms',
                'invoice.fechaOrden',
                'invoice.salesPerson',
                'invoice.orden',
                'invoice.peso',
                'invoice.cantidad',
                'cliente.nombre AS nombreCliente',
                'invoice.subTotal',
                'invoice.TotalEnvio'
            )
            ->leftJoin('cliente', 'invoice.cliente', '=', 'cliente.id');

        $invoices = $query->paginate($perPage, ['*'], 'page', $page);

        $response = [
            'data' => [
                'headers' => [],
                'original' => [
                    'status' => Response::HTTP_OK,
                    'total' => $invoices->total(),
                    'cantidad_por_pagina' => $invoices->perPage(),
                    'pagina' => $invoices->currentPage(),
                    'cantidad_total' => $invoices->total(),
                    'results' => $invoices->items(),
                ],
                'exception' => null,
            ],
        ];

        return response()->json($response);

    }

    public function invoicesReport(Request $request)
    {
        try {
            $sql = "SELECT
            invoice.id AS id,
            invoice.fecha,
            invoice.cliente,
            invoice.total,
            invoice.formaDePago,
            invoice.estado,
            invoice.observaciones,
            invoice.anulada,
            invoice.billTo,
            invoice.shipTo,
            invoice.shipVia,
            invoice.FOB,
            invoice.Terms,
            invoice.fechaOrden,
            invoice.salesPerson,
            invoice.orden,
            invoice.peso,
            invoice.cantidad,
            cliente.nombre AS nombreCliente,
            invoice.subTotal,
            invoice.TotalEnvio
        FROM
            invoice
        LEFT JOIN
            cliente ON invoice.cliente = cliente.id";

            $invoices = DB::select($sql);

            // Convertir los objetos stdClass en arrays asociativos
            $invoices = array_map(function ($item) {
                return (array) $item;
            }, $invoices);

            $cabeceras = [
                'id', 'fecha', 'cliente', 'total', 'forma de pago', 'estado', 'observaciones', 'anulada', 'billto', 'shipto', 'shipvia', 'fob', 'terms', 'fecha orden', 'sales person', 'orden',
                'peso', 'cantidad', 'nombre cliente', 'subtotal', ' total envio'
            ];

            $response = ArrayToXlsxHelper::getXlsx($invoices, $cabeceras);

            return $response;

            //     return response()->json(['data' => $invoices], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los invoices'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
