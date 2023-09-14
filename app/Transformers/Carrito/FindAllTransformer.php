<?php

namespace App\Transformers\Carrito;

use App\Models\Cliente;
use App\Helpers\CalcHelper;
use App\Models\Carritodetalle;
use App\Helpers\CalcEnvioHelper;
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
                'subTotal' => $subTotal * $detalle->cantidad,
            ];

            return $detalleCarrito;
        });

        $total = $response->sum('subTotal');

        $subTotal = $response->sum('subTotal'); 
        $cantidades = $response->sum('cantidad');
        $totalEnvio = CalcEnvioHelper::calcular($cantidades);
        $descuentos = '0.00';
        $total = $subTotal-$descuentos;
        $totalConEnvio = $total+$totalEnvio;

        return [
        'carrito' => $id, 
        'total' => $total == 0 ? '0.00' : $total,
        'descuentos'=> $descuentos == 0 ? '0.00' : $descuentos, 
        'subtotal'=> $subTotal == 0 ? '0.00' : $subTotal, 
        'totalConEnvio'=> $totalConEnvio == 0 ? '0.00' : $totalConEnvio, 
        'totalEnvio' => $totalEnvio == 0 ? '0.00' : $totalEnvio,
        'detalles' => $response->toArray(),
        'cantidadUnidades' => $cantidades,
        'montoMaximoDePago' => $cliente->montoMaximoDePago
    ];
    }
}
