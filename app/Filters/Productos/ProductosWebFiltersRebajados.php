<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Transformers\Productos\FindAllTransformer;
use App\Transformers\Productos\FindAllWebTransformer;

class ProductosWebFiltersRebajados
{

        public static function getPaginateProducts($request)
    {
        $page    = max(1, (int) $request->input('pagina', env('PAGE', 1)));
        $perPage = max(1, (int) $request->input('cantidad', env('PER_PAGE', 15)));
        $offset  = ($page - 1) * $perPage;

        //Consulta original, literal
        $SQL = "SELECT
                    producto.id AS producto_id,
                    CONCAT('https://phpstack-1091339-3819555.cloudwaysapps.com/storage/app/public/images/',
                    COALESCE(
                        IF(producto.proveedorExterno='nywd',
                            IF(
                                fotoproducto.descargada = 2,
                                SUBSTRING_INDEX(fotoproducto.url, '/', -1),
                                '0.jpg'
                            ),
                        CONCAT(producto.imagenPrincipal, '.jpg')
                        ),
                        '0.jpg'
                    )) AS imagenPrincipal,
                    producto.codigo,
                    producto.nombre,
                    producto.categoria,
                    categoriaproducto.id AS categoria_id,
                    categoriaproducto.nombre AS categorias,
                    producto.precio,
                    producto.stock,
                    producto.destacado,
                    producto.marca,
                    marcaproducto.id AS marca_id,
                    marcaproducto.nombre AS marca_nombre,
                    producto.color AS color,
                    producto.precioPromocional,
                    producto.nuevo,
                    producto.suspendido,
                    CASE
                        WHEN producto.marca IN (359,789, 797, 800, 787) THEN 0
                        ELSE 3
                    END AS orden
                FROM producto
                LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
                LEFT JOIN categoriaproducto ON producto.categoria = categoriaproducto.id
                LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
                WHERE
                    producto.precioPromocional > 0
                    AND producto.precioPromocional <= 9.99
                    AND producto.precio >= producto.precioPromocional
                    AND producto.stock > 0
                    AND producto.suspendido = 0
                    AND producto.borrado IS NULL
                ORDER BY
                    orden ASC,
                    marcaproducto.nombre ASC,
                    producto.ultimoIngresoDeMercaderia DESC,
                    producto.id ASC";

        //Conteo total
        $countQuery = "SELECT COUNT(*) as total
                    FROM producto
                    LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
                    LEFT JOIN categoriaproducto ON producto.categoria = categoriaproducto.id
                    WHERE
                        producto.precioPromocional > 0
                        AND producto.precioPromocional <= 9.99
                        AND producto.precio >= producto.precioPromocional
                        AND producto.stock > 0
                        AND producto.suspendido = 0
                        AND producto.borrado IS NULL";

        $total = DB::selectOne($countQuery)->total;

        //Agregar paginación a la consulta original
        $paginatedSQL = $SQL . " LIMIT {$perPage} OFFSET {$offset}";

        //Ejecutar consulta
        $items = DB::select($paginatedSQL);

        //Aplicar transformación (si la necesitás)
        $transformer = new FindAllWebTransformer();
        $productosTransformados = collect($items)->map(function ($producto) use ($transformer) {
            return $transformer->transform($producto);
        });

        //Paginador manual
        $paginator = new LengthAwarePaginator(
            $productosTransformados,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        //Respuesta
        return response()->json([
            'status'              => Response::HTTP_OK,
            'total'               => $paginator->total(),
            'cantidad_por_pagina' => $paginator->perPage(),
            'pagina'              => $paginator->currentPage(),
            'cantidad_total'      => $paginator->total(),
            'results'             => $paginator->items(),
        ]);
    }
    // public static function getPaginateProducts($request, $model)
    // {
    //     // Obtén los parámetros de la solicitud
    //     $page = $request->input('pagina', env('PAGE'));
    //     $perPage = $request->input('cantidad', env('PER_PAGE'));

    //     // Inicializa la consulta utilizando el modelo
    //     $query = $model::query();

    //     $query->join('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
    //         ->join('categoriaproducto', 'producto.categoria', '=', 'categoriaproducto.id')
    //         ->select(
    //             'producto.id as producto_id',
    //             'producto.imagenPrincipal',
    //             'producto.codigo',
    //             'producto.nombre',
    //             'producto.categoria',
    //             'categoriaproducto.id as categoria_id',
    //             'categoriaproducto.nombre as categoria_nombre',
    //             'producto.precio',
    //             'producto.stock',
    //             'producto.destacado',
    //             'producto.marca',
    //             'marcaproducto.nombre as marca_nombre',
    //             'producto.color as color_nombre',
    //             'producto.precioPromocional',
    //             'producto.nuevo',
    //             'producto.suspendido'
    //         )
    //         ->where(function ($query) {
    //             $query->where('producto.precioPromocional', '>', 0)
    //                 ->where('producto.precioPromocional', '<', 9.99)
    //                 ->where('producto.precio', '>=', DB::raw('producto.precioPromocional'));
    //         })
    //         ->where('producto.stock', '>', 0)
    //         ->where('producto.suspendido', '=', 0)
    //         ->whereNull('producto.borrado')
    //         ->orderBy('marcaproducto.nombre', 'asc')
    //         ->orderBy('producto.ultimoIngresoDeMercaderia', 'desc')
    //         ->orderBy('producto.id', 'asc');

    //     // Pagina los resultados
    //     $data = $query->paginate($perPage, ['*'], 'page', $page);


    //     // Crea una instancia del transformer
    //     $transformer = new FindAllWebTransformer();

    //     // Transforma cada producto individualmente
    //     $productosTransformados = $data->map(function ($producto) use ($transformer) {
    //         return $transformer->transform($producto);
    //     });

    //     $response = [
    //         'status' => Response::HTTP_OK,
    //         'total' => $data->total(),
    //         'cantidad_por_pagina' => $data->perPage(),
    //         'pagina' => $data->currentPage(),
    //         'cantidad_total' => $data->total(),
    //         'results' => $productosTransformados,
    //     ];

    //     return response()->json($response);
    // }
}
