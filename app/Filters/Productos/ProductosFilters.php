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
        $nombreMarca = $request->input('nombreMarca');
        $precioDesde = $request->input('precioDesde');
        $precioHasta = $request->input('precioHasta');
        $stockDesde = $request->input('stockDesde');
        $stockHasta = $request->input('stockHasta');
        $tipo = $request->input('tipo');
        $marca = $request->input('marca');
        $idMarca = $request->input('idMarca');
        $material = $request->input('material');
        $color = $request->input('color');
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
        $query->nombreMarca($nombreMarca);
        $query->suspendido($suspendido);
        $query->precioRange($precioDesde, $precioHasta);
        $query->stockRange($stockDesde, $stockHasta);
        $query->tipo($tipo);
        $query->idMarca($idMarca);
        $query->material($material);
        $query->color($color);
        $query->destacado($destacado);
        $query->grupo($grupo);

        // /*
        // * filtro completo para productos nuevos
        // */
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

        // /*
        // * filtro completo para buscador general
        // */
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

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada producto individualmente
        $productosTransformados = $data->map(function ($producto) use ($transformer) {
            return $transformer->transform($producto);
        });

        $response = [
            'status' => Response::HTTP_OK,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $productosTransformados,
        ];

        return response()->json($response);
    }
}
