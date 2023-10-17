<?php

namespace App\Helpers;

use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Models\Cupondescuento;

class CalcCuponHelper
{
    public static function calcularDescuento($cuponId, $total, $totalDescuentoPromociones)
    {
        $cupon = Cupondescuento::find($cuponId);

        $carrito = CarritoHelper::getCarrito();

        $descuentoPorCupon = 0;

        if ($cupon && $total  >= $cupon->montoMinimo) {
            $descuentoPorCupon = $cupon->descuentoFijo + ($total  * ($cupon->descuentoPorcentual / 100));
        }

        $carritoDetallesProductos = Carritodetalle::where('carrito', $carrito['id'])->get();

        if ($cupon && ($cupon->combinable || $totalDescuentoPromociones == 0)) {
            $decTotalPrecioEnPromocion = 0;
            $intCantidadEnPromocion = 0;

            if ($cupon->marca !== 0) {

                $carritoDetalles = Carritodetalle::where('carrito', $carrito['id'])->get();

                foreach ($carritoDetalles as $carritoDetalle) {
                    $decTotalPrecioEnPromocion += ($carritoDetalle->cantidad * $carritoDetalle->precio);
                    $intCantidadEnPromocion += $carritoDetalle->cantidad;
                }

                if ($decTotalPrecioEnPromocion >= $cupon->montoMinimo && $cupon->cantidadMinima <= $intCantidadEnPromocion) {
                    $descuentoPorCupon = $cupon->descuentoFijo + ($decTotalPrecioEnPromocion * ($cupon->descuentoPorcentual / 100));
                }
            } else {

                $intCantidadProductos = $carritoDetallesProductos->sum('cantidad');

                if ($total >= $cupon->montoMinimo && $cupon->cantidadMinima <= $intCantidadProductos) {
                    $descuentoPorCupon = $cupon->descuentoFijo + ($total * ($cupon->descuentoPorcentual / 100));
                }
            }
        }
        return $descuentoPorCupon;
    }
}
