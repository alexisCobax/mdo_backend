<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use App\Models\Marcaproducto;
use Illuminate\Http\Response;
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
        $material = $request->input('material');
        $color = $request->input('color');
        $precioDesde = $request->input('precioDesde');
        $precioHasta = $request->input('precioHasta');
        $stockDesde = $request->input('stockDesde');
        $stockHasta = $request->input('stockHasta');
        $destacado = $request->input('destacado');
        $estado = $request->input('estado');
        $buscador = $request->input('buscador');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query();

        // Aplica los filtros si se proporcionan
        $query->codigo($codigo);
        $query->categoria($categoria);
        $query->nombre($nombre);
        $query->suspendido($suspendido);
        $query->tipo($tipo);
        $query->material($material);
        $query->color($color);
        $query->precioRange($precioDesde, $precioHasta);
        $query->stockRange($stockDesde, $stockHasta);
        $query->destacado($destacado);

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
        if($buscador){

            $data = Producto::where('descripcion', 'LIKE', "%$buscador%")
            ->orWhere('tamano', 'LIKE', "%$buscador%")
            // ->orWhere('talle', 'LIKE', "%$buscador%")
            ->orWhere('nombre', 'LIKE', "%$buscador%")
            ->orWhereHas('marcaBuscador', function ($query) use ($buscador) {
                $query->where('nombre', 'LIKE', "%$buscador%");
            })
            ->orWhereHas('colorBuscador', function ($query) use ($buscador) {
                $query->where('nombre', 'LIKE', "%$buscador%");
            })
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

            // // Crea la respuesta personalizada
            // $response = [
            //     'status' => Response::HTTP_OK,
            //     'results' => $productosTransformados,
            // ];

            // // Devuelve la respuesta
            // return response()->json($response);

        }



        if ($marca) {
            if (is_numeric($marca)) {
                $query->marca($marca);
            } else {
                $marcas = Marcaproducto::where('nombre', 'LIKE', '%' . $marca . '%')->pluck('id')->toArray();
                $query->whereIn('marca', $marcas);
            }
        }

        //Realiza la paginación de la consulta
        $data = $query->where('precio', '>', 0)
            ->where('stock', '>', 0)
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
}
