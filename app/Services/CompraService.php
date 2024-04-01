<?php

namespace App\Services;

use App\Filters\Compras\ComprasFilters;
use App\Filters\Compras\ComprasProductoFilters;
use App\Models\Compra;
use App\Models\Compradetalle;
use App\Models\Compradetallenn;
use App\Transformers\Compra\FindByIdTransformer;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CompraService
{
    public function findAll(Request $request)
    {

        try {
            if ($request->codigo) {
                $data = ComprasProductoFilters::getPaginateCompras($request, Compra::class);
            } else {
                $data = ComprasFilters::getPaginateCompras($request, Compra::class);
            }


            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener las compras', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        try {
            $transformer = new FindByIdTransformer($request);
            $transformer = $transformer->transform();

            return response()->json(['data' => $transformer], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener la compra'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request)
    {
        $compra = new Compra();

        if (!$compra) {
            return response()->json(['error' => 'Failed to create compra'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $compra->proveedor = $request->proveedor;
        $compra->fechaDeIngreso = $request->fechaDeIngreso;
        $compra->fechaDePago = $request->fechaDePago;
        $compra->precio = $request->precio;
        $compra->numeroLote = $request->numeroLote;
        $compra->observaciones = $request->observaciones;
        $compra->pagado = $request->pagado;
        $compra->enDeposito = 0;
        $compra->save();
        $compraId = $compra->id;
        $precio = 0;
        if ($request->productos) {
            foreach ($request->productos as $p) {
                $precio += $p['precioUnitario'] * $p['cantidad'];
                $compraDetalle = new Compradetalle();
                $compraDetalle->compra = $compraId;
                $compraDetalle->producto = $p['producto'];
                $compraDetalle->cantidad = $p['cantidad'];
                $compraDetalle->precioUnitario = $p['precioUnitario'];
                $compraDetalle->enDeposito = 0;
                $compraDetalle->precioVenta = $p['precioVenta'];
                $compraDetalle->save();
            }
        }

        if ($request->gastos) {
            foreach ($request->gastos as $g) {
                $precio += $g['precioGasto'];
                $compraGastos = new Compradetallenn();
                $compraGastos->descripcion = $g['descripcion'];
                $compraGastos->precio = $g['precioGasto'];
                $compraGastos->idCompra = $compraId;
                $compraGastos->save();
            }
        }

        $compraPrecio = Compra::where('id', $compraId)->first();
        $compraPrecio->precio = $precio;
        $compraPrecio->save();

        $sql = "UPDATE compradetalle
        LEFT JOIN producto ON compradetalle.producto = producto.id
        SET producto.precio = compradetalle.precioVenta
        WHERE compradetalle.compra = {$compraId}";

        try {
            DB::statement($sql);
        } catch (Error $e) {
            return response()->json("Error: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }


        return response()->json($compra, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        try {
            DB::update("
            UPDATE compradetalle
            LEFT JOIN producto ON compradetalle.producto = producto.id
            SET producto.stock = producto.stock - compradetalle.cantidad
            WHERE compradetalle.compra = {$request->id} AND compradetalle.enDeposito = 1
        ");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $compra = Compra::find($request->id);

        if (!$compra) {
            return response()->json(['error' => 'Compra not found'], Response::HTTP_NOT_FOUND);
        }

        $compra->proveedor = $request->proveedor;
        $compra->fechaDeIngreso = $request->fechaDeIngreso;
        $compra->fechaDePago = $request->fechaDePago;
        $compra->precio = $request->precio;
        $compra->numeroLote = $request->numeroLote;
        $compra->observaciones = $request->observaciones;
        $compra->pagado = $request->pagado;
        $compra->enDeposito = $request->enDeposito;
        $compra->save();

        if ($request->productos) {

            try {
                CompraDetalle::where('compra', $request->id)->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar los detalles de compra.']);
            }
            $precio = 0;
            $compra->enDeposito = 1;
            foreach ($request->productos as $p) {
                $precio += $p['precioUnitario'] * $p['cantidad'];
                $compraDetalle = new Compradetalle();
                $compraDetalle->compra = $request->id;
                $compraDetalle->producto = $p['producto'];
                $compraDetalle->cantidad = $p['cantidad'];
                $compraDetalle->precioUnitario = $p['precioUnitario'];
                $compraDetalle->enDeposito = $p['enDeposito'];
                $compraDetalle->precioVenta = $p['precioVenta'];
                $compraDetalle->save();
                if ($p['enDeposito'] == 0) {
                    $compra->enDeposito = 0;
                }
            }
            $compra->save();
        }

        if ($request->gastos) {

            try {
                CompraDetalle::where('idCompra', $request->id)->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar los detalles NN de compra.']);
            }

            foreach ($request->gastos as $g) {
                $precio += $g['precioGasto'];
                $compraDetallenn = new Compradetallenn();
                $compraDetallenn->idCompra = $request->id;
                $compraDetallenn->descripcion = $g['descripcion'];
                $compraDetallenn->precio = $g['precioGasto'];
                $compraDetallenn->save();
            }
        }

        $compraPrecio = Compra::where('id', $request->id)->first();
        $compraPrecio->precio = $precio;
        $compraPrecio->save();

        try {
            DB::update("
            UPDATE  compradetalle
	        LEFT JOIN producto on compradetalle.producto = producto.id
		    SET producto.stock = producto.stock + compradetalle.cantidad
	        WHERE compradetalle.compra = {$request->id} and compradetalle.enDeposito= 1;
        ");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $sql = "UPDATE compradetalle
        LEFT JOIN producto ON compradetalle.producto = producto.id
        SET producto.precio = compradetalle.precioVenta
        WHERE compradetalle.compra = {$request->id}";

        try {
            DB::statement($sql);
        } catch (Error $e) {
            return response()->json("Error: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($compra, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $compra = Compra::find($request->id);

        if (!$compra) {
            return response()->json(['error' => 'Compra not found'], Response::HTTP_NOT_FOUND);
        }

        $compra->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
