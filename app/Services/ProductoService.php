<?php

namespace App\Services;

use App\DataTransferObject\ProductoDTO;
use App\Filters\Productos\ProductosFilters;
use App\Helpers\ImagesHelper;
use App\Models\Fotoproducto;
use App\Models\Producto;
use App\Transformers\Productos\FindByIdTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = ProductosFilters::getPaginateProducts($request, Producto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stock(Request $request)
    {
        try {
            $data = Producto::find($request->id);
            $data = [
                'stock' => $data->stock,
                'minimo' => $data->stockMinimo,
                'alarma' => $data->alarmaStockMinimo,
                'roto' => $data->stockRoto,
                'falabella' => $data->stockFalabella,
            ];

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        try {
            $data = collect([Producto::find($request->id)]);

            if ($data[0]) {

                $transformer = new FindByIdTransformer();

                $productosTransformados = $data->map(function ($producto) use ($transformer) {
                    return $transformer->transform($producto);
                });

                $response = [
                    'status' => Response::HTTP_OK,
                    'message' => $productosTransformados,
                ];

                return response()->json(['data' => $response], Response::HTTP_OK);
            } else {
                return response()->json(['data' => null], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener el productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {

        $data = $request->all();
        $productDTO = new ProductoDTO($data, $request);
        $datosProducto = $productDTO->datosProducto;

        $producto = Producto::create($datosProducto);

        if ($request->images) {

            $uploader = new ImagesHelper();
            $imagenes = $uploader->uploadMultipleImages($request, 'images');

            foreach ($imagenes as $img) {

                $imagen = new Fotoproducto();
                $imagen->idProducto = $producto->id;
                $imagen->orden = 0;
                $imagen->save();

                $currentPath = 'public/' . $img;
                $newPath = 'public/images/' . $imagen->id . '.' . env('EXTENSION_IMAGEN_PRODUCTO');

                if (Storage::exists($currentPath)) {
                    Storage::move($currentPath, $newPath);
                }
            }

            $producto = Producto::find($producto->id);
            $producto->imagenPrincipal = $imagen->id;
            $producto->save();
        }

        if (!$producto) {
            return response()->json(['error' => 'Failed to create Producto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($producto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        $producto = Producto::find($request->id);

        if (!$producto) {
            return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $datosProducto = [
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'categoria' => $request->categoria,
            'marca' => $request->marca,
            'material' => $request->material,
            'estuche' => $request->estuche,
            'sexo' => $request->sexo,
            'proveedor' => $request->proveedor,
            'precio' => $request->precio,
            'suspendido' => $request->suspendido,
            'comision' => $request->comision,
            'stock' => $request->stock,
            'stockMinimo' => $request->stockMinimo,
            'codigo' => $request->codigo,
            'alarmaStockMinimo' => $request->alarmaStockMinimo,
            'color' => $request->color,
            'tamano' => $request->tamano,
            'ubicacion' => $request->ubicacion,
            'grupo' => $request->grupo,
            'pagina' => $request->pagina,
            'costo' => $request->costo,
            'bestBrasil' => $request->bestBrasil,
            'posicion' => $request->posicion,
            'stockRoto' => $request->stockRoto,
            'ultimoIngresoDeMercaderia' => $request->ultimoIngresoDeMercaderia,
            'ultimaVentaDeMercaderia' => $request->ultimaVentaDeMercaderia,
            'genero' => $request->genero,
            'UPCreal' => $request->UPCreal,
            'mdoNet' => $request->mdoNet,
            'jet' => $request->jet,
            'precioJet' => $request->precioJet,
            'stockJet' => $request->stockJet,
            'multipack' => $request->multipack,
            'nodeJet' => $request->nodeJet,
            'nombreEN' => $request->nombreEN,
            'descripcionEN' => $request->descripcionEN,
            'peso' => $request->peso,
            'enviadoAJet' => $request->enviadoAJet,
            'stockFalabella' => $request->stockFalabella,
            'precioFalabella' => $request->precioFalabella,
            'verEnFalabella' => $request->verEnFalabella,
            'enviadoAFalabella' => $request->enviadoAFalabella,
            'categoriaFalabella' => $request->categoriaFalabella,
            'marcaFalabella' => $request->marcaFalabella,
            'descripcionFalabella' => $request->descripcionFalabella,
            'precioPromocional' => $request->precioPromocional,
            'destacado' => $request->destacado,
            'largo' => $request->largo,
            'alto' => $request->alto,
            'ancho' => $request->ancho,
            'descripcionLarga' => $request->descripcionLarga,
            'colorPrincipal' => $request->colorPrincipal,
            'colorSecundario' => $request->colorSecundario,
        ];

        $producto->update($datosProducto);

        if ($request->images) {
            $uploader = new ImagesHelper();
            $imagenes = $uploader->uploadMultipleImages($request, 'images');

            foreach ($imagenes as $img) {
                $imagen = new Fotoproducto();
                $imagen->idProducto = $producto->id;
                $imagen->orden = 0;
                $imagen->save();

                $currentPath = 'public/' . $img;
                $newPath = 'public/images/' . $imagen->id . '.' . env('EXTENSION_IMAGEN_PRODUCTO');

                if (Storage::exists($currentPath)) {
                    Storage::move($currentPath, $newPath);
                }
            }

            $producto->imagenPrincipal = $imagen->id;
            $producto->save();
        }

        return response()->json($producto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $producto = Producto::find($request->id);

        if (!$producto) {
            return response()->json(['error' => 'Producto not found'], Response::HTTP_NOT_FOUND);
        }
        try {
            $producto->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al borrar el producto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['id' => $request->id], 200);
    }

    public function related(Request $request)
    {
        $producto = Producto::select('*')
            ->where('categoria', '=', $request->categoria)
            ->where('id', '!=', $request->producto)
            ->where('stock', '>', 0)
            ->orderBy('id', 'DESC')
            ->limit(4)
            ->get();
 
        if (!$producto) {
            return response()->json(['error' => 'Related product not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($producto, Response::HTTP_OK);
    }
}
