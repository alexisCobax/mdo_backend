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

            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $productos = Producto::where('id', $request->id)->get();

            if ($productos->count() != 0) {

                $transformer = new FindByIdTransformer();

                $productosTransformados = $productos->map(function ($producto) use ($transformer) {
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
            return response()->json(['error' => $e->getMessage(). ' Ocurrió un error al obtener el productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByCodigo(Request $request)
    {
        try {

            $data = collect([Producto::where('codigo', $request->codigo)->first()]);

            if ($data[0]) {

                $transformer = new FindByIdTransformer();

                $productosTransformados = $data->map(function ($producto) use ($transformer) {
                    return $transformer->transform($producto);
                });

                $response = [
                    'status' => Response::HTTP_OK,
                    'message' => $productosTransformados,
                ];

                return response()->json(['data' => $response, 'status' => 200], Response::HTTP_OK);
            } else {
                $response = ['status' => 201,'message' => [],];
                return response()->json(['data' => $response, 'status' => 201], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                $newPath = 'public/images/' . $imagen->id . '.jpg';

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
            //'ultimoIngresoDeMercaderia' => date("Y-m-d H:i:s"),
            //'ultimaVentaDeMercaderia' => date("Y-m-d H:i:s"),
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
            'nuevo' => $request->nuevo,
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
                $newPath = 'public/images/' . $imagen->id . '.jpg';

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
            $producto->borrado = now();
            $producto->save();
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

    public function precioGeneral(Request $request){

        try {
            $strCondicion = "";
            $strCambios = "";

            $query = "UPDATE producto SET " . $strCambios . " WHERE " . substr($strCondicion, 4);

            $bindings = [];

            if ($intMarca != 0) {
                $strCondicion .= " AND marca = ?";
                $bindings[] = $intMarca;
            }
            if ($intTipo != 0) {
                $strCondicion .= " AND tipo = ?";
                $bindings[] = $intTipo;
            }
            if ($intCategoria != 0) {
                $strCondicion .= " AND categoria = ?";
                $bindings[] = $intCategoria;
            }
            if ($intColor != 0) {
                $strCondicion .= " AND color = ?";
                $bindings[] = $intColor;
            }
            if ($intGrupo != 0) {
                $strCondicion .= " AND grupo = ?";
                $bindings[] = $intGrupo;
            }
            if ($blnFiltrarPagina) {
                $strCondicion .= " AND pagina = ?";
                $bindings[] = $blnPagina;
            }
            if ($blnFiltrarEstuche) {
                $strCondicion .= " AND estuche = ?";
                $bindings[] = $intEstuche;
            }
            if ($blnFiltrarSuspendido) {
                $strCondicion .= " AND suspendido = ?";
                $bindings[] = $blnSuspendido;
            }
            if ($blnFiltrarStock) {
                $strCondicion .= " AND stock BETWEEN ? AND ?";
                $bindings[] = $intStockDesde;
                $bindings[] = $intStockHasta;
            }
            if ($blnFiltrarPrecio) {
                $strCondicion .= " AND precio BETWEEN ? AND ?";
                $bindings[] = $decPrecioDesde;
                $bindings[] = $decPrecioHasta;
            }

            if ($blnCambiarPrecio) {
                $strCambios = " precio = ?";
                $bindings[] = $decCambioPrecio;
            }

            if ($blnCambiarCosto) {
                $strCambios = " costo = ?";
                $bindings[] = $decCambioCosto;
            }

            if ($blnCambiarDescuentoPorcentual) {
                $strCambios = " precio = (precio + (? * precio / 100))";
                $bindings[] = $decCambioDescuentoPorcentual;
            }

            if ($blnCambiarDescuento) {
                $strCambios = " precio = (precio + ?)";
                $bindings[] = $decCambioDescuento;
            }

            if ($blnCambiarEstuche) {
                $strCambios = " estuche = ?";
                $bindings[] = $intCambiarEstuche;
            }

            if ($blnCambiarEstadoSuspendio) {
                $strCambios = " suspendido = ?";
                $bindings[] = $blnNuevoEstadoSuspendido;
            }

            $result = DB::update($query, $bindings);

            return $result !== false;
        } catch (Exception $ex) {
            // Handle the exception
            return false;
        }

        return true;

    }
}
