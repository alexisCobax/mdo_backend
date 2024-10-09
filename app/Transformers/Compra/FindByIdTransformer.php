<?php

namespace App\Transformers\Compra;

use App\Models\Compra;
use App\Models\Compradetalle;
use App\Models\Compradetallenn;
use League\Fractal\TransformerAbstract;

class FindByIdTransformer extends TransformerAbstract
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function transform()
    {
        $data = collect([Compra::find($this->request->id)]);

        $compraDetalle = $data->map(function ($compras) {

            $compraDetalle = Compradetalle::where('compra', $compras->id)->get();
            $productos = [];

            foreach ($compraDetalle as $c) {
                $productos[] = [
                    'id' => $c->id,
                    'compra' => $c->compra,
                    'producto' => $c->producto,
                    'codigo' => optional($c->productos)->codigo,
                    'nombreProducto' => optional($c->productos)->nombre,
                    'cantidad' => $c->cantidad,
                    'precioUnitario' => $c->precioUnitario,
                    'precioVenta' => $c->precioVenta,
                    'enDeposito' => $c->enDeposito,
                ];
            }

            $compraDetallenn = Compradetallenn::where('idCompra', $compras->id)->get();
            $gastos = [];


            foreach ($compraDetallenn as $cd) {
                $precioGasto = number_format(floatval(str_replace(',', '', $cd->precio)), 2, '.', '');

                $gastos[] = [
                    'id' => $cd->id,
                    'descripcion' => $cd->descripcion,
                    'precioGasto' => $precioGasto,
                    'idCompra' => $cd->idCompra,
                ];
            }

            return [
                'id' => $compras->id,
                'proveedor' => $compras->proveedor,
                'nombreProveedor' => optional($compras->proveedores)->nombre,
                'fechaDeIngreso' => $compras->fechaDeIngreso,
                'fechaDePago' => $compras->fechaDePago,
                'precio' => $compras->precio,
                'numeroLote' => $compras->numeroLote,
                'observaciones' => $compras->observaciones,
                'pagado' => $compras->pagado,
                'enDeposito' => $compras->enDeposito,
                'productos' => $productos,
                'gastos' => $gastos,
            ];
        })->toArray();

        return $compraDetalle;
    }
}
