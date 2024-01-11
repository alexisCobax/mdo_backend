<?php

namespace App\Transformers\Carrito;

use App\Helpers\CalcHelper;
use App\Helpers\CalcTotalHelper;
use App\Models\Carrito;
use App\Models\Carritodetalle;
use App\Models\Cliente;
use App\Services\DescuentosService;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform($id)
    {

        $user = Auth::user();

        $cliente = Cliente::where('usuario', $user['id'])->first();

        $detalle = Carritodetalle::where('carrito', $id)->get();

        $descuentosService = new DescuentosService;

        $carrito = Carrito::where('id', $id)->first();

        $response = $detalle->map(function ($detalle) {

            $subTotal = CalcHelper::ListProduct(optional($detalle->productos)->precio, optional($detalle->productos)->precioPromocional);

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
                'subTotal' => $subTotal * $detalle->cantidad,
            ];

            return $detalleCarrito;
        });

        $subTotal = $response->sum('subTotal');
        $cantidades = $response->sum('cantidad');
        $descuentos = '0.00';

        $calculo = CalcTotalHelper::calcular($subTotal, $cantidades, $descuentos);

        $descuento = $descuentosService->discount($carrito->cupon, $calculo['total'], $calculo['descuentos']);

        $total = $calculo['total'];

        if($descuento){
            $total = $calculo['total']-$descuento;
        }
        
        return [
            'carrito' => $id,
            'total' => $total == 0 ? '0.00' : number_format($total,2),
            'descuentos' => $descuento == 0 ? '0.00' : number_format($descuento,2),
            'subtotal' => $calculo['subTotal'] == 0 ? '0.00' : $calculo['subTotal'],
            'totalConEnvio' => $calculo['totalConEnvio'] == 0 ? '0.00' : $calculo['totalConEnvio'],
            //'totalEnvio' => $calculo['totalEnvio'] == 0 ? '0.00' : $calculo['totalEnvio'],
            'totalEnvio' => '10.00',
            'detalles' => $response->toArray(),
            'cantidadUnidades' => $cantidades,
            'montoMaximoDePago' => $cliente->montoMaximoDePago,
        ];
    }
}
