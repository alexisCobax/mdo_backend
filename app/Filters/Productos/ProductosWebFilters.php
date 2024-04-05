<?php

namespace App\Filters\Productos;

use App\Models\Producto;
use Illuminate\Http\Response;
use App\Transformers\Productos\FindAllTransformer;
use App\Transformers\Productos\FindAllWebTransformer;

class ProductosWebFilters
{
    public static function getPaginateProducts($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // // Obtén los parámetros del filtro
        // $codigo = $request->input('codigo');
        // $categoria = $request->input('categoria');
        // $nombre = $request->input('nombre');
        // $suspendido = $request->input('suspendido');
        // $nombreMarca = $request->input('nombreMarca');
        // $precioDesde = $request->input('precioDesde');
        // $precioHasta = $request->input('precioHasta');
        // $stockDesde = $request->input('stockDesde');
        // $stockHasta = $request->input('stockHasta');
        // $tipo = $request->input('tipo');
        // $marca = $request->input('marca');
        // $idMarca = $request->input('idMarca');
        // $material = $request->input('material');
        // $color = $request->input('color');
        // $destacado = $request->input('destacado');
        // $estado = $request->input('estado');
        // $buscador = $request->input('buscador');
        // $grupo = $request->input('grupo');
        // $tag = $request->input('tag');

        // // Inicializa la consulta utilizando el modelo
        // $query = $model::query();

        // // Aplica los filtros si se proporcionan
        // $query->codigo($codigo);
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

        // switch ($tag) {
        //     case 'precioPromocional':
        //         $query->PrecioPromocional();
        //         break;
        //     case 'menos20':
        //         $query->Menos20();
        //         break;
        // }

        // $query->join('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
        //     ->select(
        //         'producto.id as producto_id', 
        //         'producto.nombre', 
        //         'producto.codigo', 
        //         'producto.categoria', 
        //         'producto.precio', 
        //         'producto.precioPromocional', 
        //         'producto.stock', 
        //         'producto.destacado', 
        //         'producto.color', 
        //         'producto.nuevo', 
        //         'producto.imagenPrincipal', 
        //         'marcaproducto.nombre as marca_nombre',
        //         'marcaproducto.id as marca_id')
        //     ->where('producto.stock', '>', 0)
        //     ->where('producto.suspendido', '=', 0)
        //     ->orderBy('marcaproducto.nombre', 'asc')
        //     ->orderBy('producto.ultimoIngresoDeMercaderia', 'desc');

        // $data = $query->paginate($perPage, ['*'], 'page', $page);

        $page = $request->input('pagina', env('PAGE'));
$perPage = $request->input('cantidad', env('PER_PAGE'));

$model = Producto::class;

$query = $model::query();

$idMarca = $request->input('idMarca');
$nombre = $request->input('nombre');

if ($idMarca) {
   $query->where('marca', '=', $idMarca);
}

if ($nombre) {
    $query->whereRaw('LOWER(producto.nombre) LIKE ?', ['%' . strtolower($nombre) . '%'])
        ->orWhereRaw('LOWER(codigo) LIKE ?', ['%' . strtolower($nombre) . '%']);
}

$query->join('marcaproducto', 'producto.marca', '=', 'marcaproducto.id')
    ->select(
        'producto.id as producto_id', 
        'producto.nombre', 
        'producto.codigo', 
        'producto.categoria', 
        'producto.precio', 
        'producto.precioPromocional', 
        'producto.stock', 
        'producto.destacado', 
        'producto.color', 
        'producto.nuevo', 
        'producto.imagenPrincipal', 
        'marcaproducto.nombre as marca_nombre',
        'marcaproducto.id as marca_id')
    ->where('producto.stock', '>', 0)
    ->where('producto.suspendido', '=', 0)
    ->orderBy('marcaproducto.nombre', 'asc')
    ->orderBy('producto.ultimoIngresoDeMercaderia', 'desc')
    ->orderBy('producto.id', 'asc'); // Añade esta línea;

$data = $query->paginate($perPage, ['*'], 'page', $page);
        // Crea una instancia del transformer
        $transformer = new FindAllWebTransformer();

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
