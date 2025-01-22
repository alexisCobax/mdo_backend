<?php

namespace App\Transformers\Carrito;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Helpers\CalcHelper;
use App\Models\Fotoproducto;
use App\Models\Carritodetalle;
use App\Models\Cupondescuento;
use App\Helpers\CalcTotalHelper;
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

        $cupon = Cupondescuento::where('id', $carrito->cupon)
        ->whereDate('inicio', '<=', date('Y-m-d'))
        ->whereDate('vencimiento', '>=', date('Y-m-d'))
        ->where('stock', '>', 0)
        ->first();

        if (!$cupon) {
            $carrito->cupon = 0;
            $carrito->save();
        }

        $response = $detalle->map(function ($detalle) {

            $imagenPrincipal = Fotoproducto::where('id',optional($detalle->productos)->imagenPrincipal)->first();

            if(isset($imagenPrincipal->url)){
                $imagen = $imagenPrincipal->url;
            }else{
                $imagen = env('URL_IMAGENES_PRODUCTOS').optional($detalle->productos)->imagenPrincipal . '.jpg';
            }

            $subTotal = CalcHelper::ListProduct(optional($detalle->productos)->precio, optional($detalle->productos)->precioPromocional);

            $producto = [
                'id' => optional($detalle->productos)->id,
                'nombre' => optional($detalle->productos)->nombre,
                'marcaNombre' => optional(optional($detalle->productos)->marcas)->nombre,
                'precio' => $subTotal,
                'imagen' => $imagen,
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

        if ($descuento) {
            $total = $calculo['total'] - $descuento;
        }

        return [
            'carrito' => $id,
            'total' => $total == 0 ? '0.00' : number_format($total, 2),
            'descuentos' => $descuento == 0 ? '0.00' : number_format($descuento, 2),
            'cupon' => optional($carrito->cupones)->nombre,
            'subtotal' => $calculo['subTotal'] == 0 ? '0.00' : $calculo['subTotal'],
            'totalConEnvio' => $calculo['totalConEnvio'] == 0 ? '0.00' : $calculo['totalConEnvio'],
            'totalEnvio' => $calculo['totalEnvio'] == 0 ? '0.00' : $calculo['totalEnvio'],
            'detalles' => $response->toArray(),
            'cantidadUnidades' => $cantidades,
            'montoMaximoDePago' => $cliente->montoMaximoDePago,
        ];
    }
}
