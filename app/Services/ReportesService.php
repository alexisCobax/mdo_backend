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
        if (isset($request->marca)) {
            $query->where('marca', $request->marca);
        }

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

            if (isset($request->marca)) {
                $sql .= " AND marca = ?";
                $stock = DB::select($sql, [$request->marca]);
            } else {
                $stock = DB::select($sql);
            }

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

        $fecha_inicio = $request->desde;
        $fecha_fin = $request->hasta;
        $marca = $request->marca;

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
            ->leftJoin('pedido', 'pedidodetalle.pedido', '=', 'pedido.id');

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('pedido.fecha', [$fecha_inicio, $fecha_fin]);
        }

        if ($marca && $marca != 'undefined') {
            $query->where('producto.marca', '=', $marca);
        }
        $query->where('pedido.estado', '<>', 4)
            ->groupBy('producto.color', 'color.nombre', 'producto.nombre', 'producto.stock', 'producto.codigo', 'producto.id', 'producto.precio', 'producto.costo')
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

            $fecha_condicion = '';

            if (!empty($request->desde) && !empty($request->hasta)) {
                $fecha_condicion = "AND pedido.fecha BETWEEN '{$request->desde}' AND '{$request->hasta}'";
            }

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
   WHERE 1=1 {$fecha_condicion} AND pedido.estado <> 4
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
    WHERE 1=1 {$fecha_condicion} AND pedido.estado <> 4
    order by 3
", [$request->desde, $request->hasta]);



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

    public function invoicesList(Request $request)
    {

        $perPage = request()->input('cantidad', env('PER_PAGE'));
        $page = request()->input('pagina', env('PAGE'));
        $fecha_desde = $request->desde . ' 00:00:00';
        $fecha_hasta = $request->hasta . ' 23:59:59';

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

            if (!empty($request->desde) && !empty($request->hasta)) {
                $query->whereBetween('invoice.fecha', [$fecha_desde, $fecha_hasta]);
            }

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

    public function topClientesList(Request $request)
    {
        $fecha_inicio = $request->filled('desde') ? $request->desde : null;
        $fecha_fin = $request->filled('hasta') ? $request->hasta : null;

        // $perPage = $request->input('cantidad', env('PER_PAGE'));
        // $page = $request->input('pagina', env('PAGE'));

        $perPage = 10;
        $page = 1;

        $query = DB::table('invoice')
            ->select(
                'cliente.id',
                'cliente.nombre',
                DB::raw('SUM(invoice.subTotal) as total')
            )
            ->leftJoin('cliente', 'cliente.id', '=', 'invoice.cliente')
            ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                return $query->whereBetween('invoice.fecha', [$fecha_inicio, $fecha_fin]);
            })
            ->when($request->filled('nombreCliente'), function ($query) use ($request) {
                return $query->where('cliente.nombre', 'like', '%' . $request->nombreCliente . '%');
            })
            ->groupBy('cliente.id', 'cliente.nombre')
            ->orderBy('total', 'desc')
            ->limit(10);
        $results = $query->paginate($perPage, ['*'], 'page', $page);


        $transformedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'nombre' => $result->nombre,
                'total' => $result->total
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

    public function topClientesReport(Request $request)
    {
        $fecha_inicio = $request->filled('desde') ? $request->desde : null;
        $fecha_fin = $request->filled('hasta') ? $request->hasta : null;
        $nombreCliente = $request->filled('nombreCliente') ? $request->nombreCliente : null;

        try {

            $fecha_inicio = $request->filled('desde') ? $request->desde : null;
            $fecha_fin = $request->filled('hasta') ? $request->hasta : null;

            $perPage = $request->input('cantidad', env('PER_PAGE'));
            $page = $request->input('pagina', env('PAGE'));

            $clientes = DB::table('invoice')
                ->select(
                    'cliente.id',
                    'cliente.nombre',
                    DB::raw('SUM(invoice.subTotal) as total')
                )
                ->leftJoin('cliente', 'cliente.id', '=', 'invoice.cliente')
                ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                    return $query->whereBetween('invoice.fecha', [$fecha_inicio, $fecha_fin]);
                })
                ->when($nombreCliente, function ($query) use ($nombreCliente) {
                    return $query->where('cliente.nombre', 'like', '%' . $nombreCliente . '%');
                })
                ->groupBy('cliente.id', 'cliente.nombre')
                ->orderBy('total', 'desc')->limit(10)->get()->toArray();

            // Convertir los objetos stdClass en arrays asociativos
            $clientes = array_map(function ($item) {
                return (array) $item;
            }, $clientes);

            $cabeceras = [
                'id', 'nombre', 'total'
            ];

            $response = ArrayToXlsxHelper::getXlsx($clientes, $cabeceras);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los invoices'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function topMarcasList(Request $request)
    {
        $fecha_inicio = $request->filled('desde') ? $request->desde : null;
        $fecha_fin = $request->filled('hasta') ? $request->hasta : null;
        $nombreMarca = $request->filled('nombreMarca') ? $request->nombreMarca : null;

        // $perPage = $request->input('cantidad', env('PER_PAGE'));
        // $page = $request->input('pagina', env('PAGE'));

        $perPage = 10;
        $page = 1;

        $query = DB::table('pedidodetalle')
        ->select('marcaproducto.id','marcaproducto.nombre as marca', DB::raw('SUM(pedidodetalle.cantidad) as cantidad'))
        ->leftJoin('pedido', 'pedido.id', '=', 'pedidodetalle.pedido')
        ->leftJoin('producto', 'producto.id', '=', 'pedidodetalle.producto')
        ->leftJoin('marcaproducto', 'marcaproducto.id', '=', 'producto.marca')
        ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
            return $query->whereBetween('pedido.fecha', [$fecha_inicio, $fecha_fin]);
        })
        ->when($nombreMarca, function ($query) use ($nombreMarca) {
            return $query->where('marcaproducto.nombre', 'like', '%' . $nombreMarca . '%');
        })
        ->groupBy('marcaproducto.id','marcaproducto.nombre')
        ->orderByDesc(DB::raw('SUM(pedidodetalle.cantidad)'))
        ->limit(10);
        $results = $query->paginate($perPage, ['*'], 'page', $page);


        $transformedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'marca' => $result->marca,
                'cantidad' => $result->cantidad
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

    public function topMarcasReport(Request $request)
    {
        $fecha_inicio = $request->filled('desde') ? $request->desde : null;
        $fecha_fin = $request->filled('hasta') ? $request->hasta : null;

        try {

            $fecha_inicio = $request->filled('desde') ? $request->desde : null;
            $fecha_fin = $request->filled('hasta') ? $request->hasta : null;
            $nombreMarca = $request->filled('nombreMarca') ? $request->nombreMarca : null;

            $perPage = $request->input('cantidad', env('PER_PAGE'));
            $page = $request->input('pagina', env('PAGE'));

            $marcas = DB::table('pedidodetalle')
            ->select('marcaproducto.id','marcaproducto.nombre as marca', DB::raw('SUM(pedidodetalle.cantidad) as cantidad'))
            ->leftJoin('pedido', 'pedido.id', '=', 'pedidodetalle.pedido')
            ->leftJoin('producto', 'producto.id', '=', 'pedidodetalle.producto')
            ->leftJoin('marcaproducto', 'marcaproducto.id', '=', 'producto.marca')
            ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                return $query->whereBetween('pedido.fecha', [$fecha_inicio, $fecha_fin]);
            })
            ->when($nombreMarca, function ($query) use ($nombreMarca) {
                return $query->where('marcaproducto.nombre', 'like', '%' . $nombreMarca . '%');
            })
            ->groupBy('marcaproducto.id','marcaproducto.nombre')
            ->orderByDesc(DB::raw('SUM(pedidodetalle.cantidad)'))
            ->limit(10)->get()->toArray();

            // Convertir los objetos stdClass en arrays asociativos
            $marcas = array_map(function ($item) {
                return (array) $item;
            }, $marcas);

            $cabeceras = [
                'id', 'nombre', 'total'
            ];

            $response = ArrayToXlsxHelper::getXlsx($marcas, $cabeceras);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los invoices'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
