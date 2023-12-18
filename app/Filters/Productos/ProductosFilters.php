<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use App\Models\Marcaproducto;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Enums\EstadosProductosEnums;
use App\Transformers\Productos\FindAllTransformer;

class ProductosFilters
{
    public static function getPaginateProducts($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $codigo = $request->input('codigo');
        $categoria = $request->input('categoria');
        $nombre = $request->input('nombre');
        $suspendido = $request->input('suspendido');
        $tipo = $request->input('tipo');
        $marca = $request->input('marca');
        $nombreMarca = $request->input('nombreMarca');
        $idMarca = $request->input('idmarca');
        $material = $request->input('material');
        $color = $request->input('color');
        $precioDesde = $request->input('precioDesde');
        $precioHasta = $request->input('precioHasta');
        $stockDesde = $request->input('stockDesde');
        $stockHasta = $request->input('stockHasta');
        $destacado = $request->input('destacado');
        $estado = $request->input('estado');
        $buscador = $request->input('buscador');
        $grupo = $request->input('grupo');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->codigo($codigo);
        $query->categoria($categoria);
        $query->nombre($nombre);
        $query->suspendido($suspendido);
        $query->tipo($tipo);
        //$query->marca($marca);
        $query->idMarca($idMarca);
        $query->material($material);
        $query->color($color);
        $query->precioRange($precioDesde, $precioHasta);
        $query->stockRange($stockDesde, $stockHasta);
        $query->destacado($destacado);
        $query->grupo($grupo);

        /*
        * filtro completo para productos nuevos
        */
        if ($estado == 'nuevo') {

            $query->NuevosProductos($estado);

            $data = $query->get();
            // Crea una instancia del transformer
            $transformer = new FindAllTransformer();

            // Transforma cada producto individualmente
            $productosTransformados = $data->map(function ($producto) use ($transformer) {
                return $transformer->transform($producto);
            });

            // Crea la respuesta personalizada
            $response = [
                'status' => Response::HTTP_OK,
                'results' => $productosTransformados,
            ];

            // Devuelve la respuesta
            return response()->json($response);
        }

        /*
        * filtro completo para buscador general
        */
        if ($buscador) {

            $data = Producto::where('descripcion', 'LIKE', "%$buscador%")
                ->orWhere('tamano', 'LIKE', "%$buscador%")
                ->orWhere('nombre', 'LIKE', "%$buscador%")
                ->orWhereHas('marcaBuscador', function ($query) use ($buscador) {
                    $query->where('nombre', 'LIKE', "%$buscador%");
                })
                ->orWhereHas('colorBuscador', function ($query) use ($buscador) {
                    $query->where('nombre', 'LIKE', "%$buscador%");
                })
                ->where('categoria', $categoria)
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Crea una instancia del transformer
            $transformer = new FindAllTransformer();

            // Transforma cada producto individualmente
            $productosTransformados = $data->map(function ($producto) use ($transformer) {
                return $transformer->transform($producto);
            });

            // Crea la respuesta personalizada
            $response = [
                'status' => Response::HTTP_OK,
                'total' => $data->total(),
                'cantidad_por_pagina' => $data->perPage(),
                'pagina' => $data->currentPage(),
                'cantidad_total' => $data->total(),
                'results' => $productosTransformados,
            ];

            // Devuelve la respuesta
            return response()->json($response);
        }

        if ($marca) {
            if (is_numeric($marca)) {
                $query->marca($marca);
            } else {
                $marcas = Marcaproducto::where('nombre', 'LIKE', '%' . $marca . '%')->pluck('id')->toArray();
                $query->whereIn('marca', $marcas);
            }
        }

        if ($nombreMarca) {

            $paginaActual = request('pagina', 1);
            $cantidadPorPagina = request('cantidad_por_pagina', 10);

            $resultado = DB::table('producto as p')
                ->select('p.*', 'mp.id as id_marca', 'mp.nombre as nombre_marca', 'c.nombre as nombre_color')
                ->join('marcaproducto as mp', 'p.marca', '=', 'mp.id')
                ->join('color as c', 'p.color', '=', 'c.id')
                ->where('mp.nombre', 'LIKE', '%' . $nombreMarca . '%')
                ->paginate($cantidadPorPagina, ['*'], 'pagina', $paginaActual);

            $productosTransformados = $resultado->map(function ($producto) {
                $arrayEnum = EstadosProductosEnums::toArray();

                return [
                    'id' => $producto->id,
                    'imagenPrincipal' => $producto->imagenPrincipal . '.' . env('EXTENSION_IMAGEN_PRODUCTO'),
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo,
                    'categoria' => $producto->categoria,
                    //'categoriaNombre' => optional($producto->categorias)->nombre,
                    'precio' => $producto->precioPromocional == 0 ? number_format($producto->precio, 2) : number_format($producto->precioPromocional, 2),
                    'precioLista' => number_format($producto->precio, 2),
                    'stock' => $producto->stock,
                    'destacado' => $producto->destacado,
                    'marca' => $producto->id_marca,
                    'nombreMarca' => $producto->nombre_marca,
                    'colorNombre' => $producto->nombre_color,
                    'precioPromocional' => number_format($producto->precioPromocional, 2),
                    'estado' => $producto->suspendido == $arrayEnum[EstadosProductosEnums::SUSPENDIDO] ? EstadosProductosEnums::SUSPENDIDO : EstadosProductosEnums::PUBLICADO,
                ];
            });

            $response = [
                'status' => Response::HTTP_OK,
                'total' => $resultado->total(),
                'cantidad_por_pagina' => $resultado->perPage(),
                'pagina' => $resultado->currentPage(),
                'cantidad_total' => $resultado->total(),
                'results' => $productosTransformados,
            ];
        }


        // //Realiza la paginación de la consulta
        // $data = $query->where('precio', '>', 0)
        //     ->where('stock', '>', 0)
        //     ->orderBy('id', 'desc')
        //     ->paginate($perPage, ['*'], 'page', $page);

        // // Crea una instancia del transformer
        // $transformer = new FindAllTransformer();

        // // Transforma cada producto individualmente
        // $productosTransformados = $data->map(function ($producto) use ($transformer) {
        //     return $transformer->transform($producto);
        // });

        // Crea la respuesta personalizada
        // $response = [
        //     'status' => Response::HTTP_OK,
        //     'total' => $data->total(),
        //     'cantidad_por_pagina' => $data->perPage(),
        //     'pagina' => $data->currentPage(),
        //     'cantidad_total' => $data->total(),
        //     'results' => $productosTransformados,
        // ];

        // Devuelve la respuesta
        return response()->json($response);
    }
}
