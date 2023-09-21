<?php

namespace App\Transformers\Carrito;

use App\Helpers\CalcHelper;
use App\Helpers\CalcTotalHelper;
use App\Models\Carritodetalle;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($id)
    {

        $user = Auth::user();

        $cliente = Cliente::where('usuario', $user['id'])->first();

        $detalle = Carritodetalle::where('carrito', $id)->get();

        $response = $detalle->map(function ($detalle) {

            $subTotal = CalcHelper::ListProduct($detalle->precio, $detalle->precioPromocional);

            $producto = [
                'id' => optional($detalle->productos)->id,
                'nombre' => optional($detalle->productos)->nombre,
                'marcaNombre' => optional(optional($detalle->productos)->marcas)->nombre,
                'precio' => $subTotal,
                'imagen' => optional($detalle->productos)->imagenPrincipal,
                'color' => optional(optional($detalle->productos)->colores)->nombre,
                'tamano' => optional($detalle->productos)->tamano,
            ];

            $detalleCarrito = [
                'id' => $detalle->id,
                'carrito' => $detalle->carrito,
                'producto' => $producto,
                'precio' => $subTotal,
                'cantidad' => $detalle->cantidad,
                'subTotal' => 'xxx'.$subTotal * $detalle->cantidad,
            ];

            return $detalleCarrito;
        });

        $subTotal = $response->sum('subTotal');
        $cantidades = $response->sum('cantidad');
        $descuentos = '0.00';

        $calculo = CalcTotalHelper::calcular($subTotal, $cantidades, $descuentos);

        return [
        'carrito' => $id,
        'total' => $calculo['total'] == 0 ? '0.00' : $calculo['total'],
        'descuentos'=> $calculo['descuentos'] == 0 ? '0.00' : $calculo['descuentos'],
        'subtotal'=> 'xxx2'.$calculo['subTotal'] == 0 ? '0.00' : $calculo['subTotal'],
        'totalConEnvio'=> $calculo['totalConEnvio'] == 0 ? '0.00' : $calculo['totalConEnvio'],
        'totalEnvio' => $calculo['totalEnvio'] == 0 ? '0.00' : $calculo['totalEnvio'],
        'detalles' => $response->toArray(),
        'cantidadUnidades' => $cantidades,
        'montoMaximoDePago' => $cliente->montoMaximoDePago,
    ];
    }
}
