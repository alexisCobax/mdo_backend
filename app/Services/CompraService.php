<?php

namespace App\Services;

use App\Filters\Compras\ComprasFilters;
use App\Models\Compra;
use App\Models\Compradetalle;
use App\Models\Compradetallenn;
use App\Transformers\Compra\FindByIdTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompraService
{
    public function findAll(Request $request)
    {

        try {
            $data = ComprasFilters::getPaginateCompras($request, Compra::class);

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

        if ($request->productos) {
            foreach ($request->productos as $p) {
                $compraDetalle = new Compradetalle();
                $compraDetalle->compra = $compraId;
                $compraDetalle->producto = $p['producto'];
                $compraDetalle->cantidad = $p['cantidad'];
                $compraDetalle->precioUnitario = $p['precioUnitario'];
                $compraDetalle->enDeposito = 0;
                $compraDetalle->save();
            }
        }

        if ($request->gastos) {
            foreach ($request->gastos as $g) {
                $compraGastos = new Compradetallenn();
                $compraGastos->descripcion = $g['descripcion'];
                $compraGastos->precio = $g['precioGasto'];
                $compraGastos->idCompra = $compraId;
                $compraGastos->save();
            }
        }

        return response()->json($compra, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $compra = Compra::find($request->id);

        if (!$compra) {
            return response()->json(['error' => 'Compra not found'], Response::HTTP_NOT_FOUND);
        }

        //hago un update de la compra
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

            $idsInJson = array_column($request->productos, 'id');
            $idsInCompraDetalle = CompraDetalle::pluck('id')->toArray();
            $idsToDelete = array_diff($idsInCompraDetalle, $idsInJson);

            if (!empty($idsToDelete)) {
                CompraDetalle::whereIn('id', $idsToDelete)->delete();
            }

            foreach ($request->productos as $p) {
                if (isset($p['id']) == '') {
                    $compraDetalle = new Compradetalle();
                    $compraDetalle->compra = $request->id;
                    $compraDetalle->producto = $p['producto'];
                    $compraDetalle->cantidad = $p['cantidad'];
                    $compraDetalle->precioUnitario = $p['precioUnitario'];
                    $compraDetalle->enDeposito = $p['enDeposito'];
                    $compraDetalle->save();
                } else {
                    $compraDetalle = Compradetalle::find($p['id']);
                    $compraDetalle->producto = $p['producto'];
                    $compraDetalle->cantidad = $p['cantidad'];
                    $compraDetalle->precioUnitario = $p['precioUnitario'];
                    $compraDetalle->enDeposito = $p['enDeposito'];
                    $compraDetalle->save();
                }
            }
        }

        if ($request->gastos) {

            $idsInJsons = array_column($request->gastos, 'id');
            $idsInCompraDetallenn = CompraDetallenn::pluck('id')->toArray();
            $idsToDeletes = array_diff($idsInCompraDetallenn, $idsInJsons);

            if (!empty($idsToDeletes)) {
                Compradetallenn::whereIn('id', $idsToDeletes)->delete();
            }

            foreach ($request->gastos as $g) {
                if (isset($g['id']) == '') {
                    $compraDetallenn = new Compradetallenn();
                    $compraDetallenn->idCompra = $request->id;
                    $compraDetallenn->descripcion = $g['descripcion'];
                    $compraDetallenn->precio = $g['precioGasto'];
                    $compraDetallenn->save();
                } else {
                    $compraDetallenn = Compradetallenn::find($g['id']);
                    $compraDetallenn->descripcion = $g['descripcion'];
                    $compraDetallenn->precio = $g['precioGasto'];
                    $compraDetallenn->save();
                }
            }
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
