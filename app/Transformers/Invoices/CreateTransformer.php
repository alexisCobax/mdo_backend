<?php

namespace App\Transformers\Invoices;

use App\Models\Encargadodeventa;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;

class CreateTransformer extends TransformerAbstract
{
    public function transform($pedido, $cantidad, $request)
    {

        $SQL = "
        SELECT SUM(total) as total
        FROM (
            SELECT IFNULL(SUM(precio * cantidad), 0) AS total
            FROM pedidodetalle
            WHERE pedido = ?
            UNION ALL
            SELECT IFNULL(SUM(precio * cantidad), 0) AS total
            FROM pedidodetallenn
            WHERE pedido = ?
        ) AS combined_totals
    ";

        $result = DB::select($SQL, [$pedido->id, $pedido->id]);

        $subTotal = $result[0]->total;
        // $subTotal = $pedido->total - $pedido->DescuentoNeto;
        $vendedorNombre = $pedido->vendedor ? optional($pedido->vendedores)->nombre : '';

        return [
            'cliente' => $pedido->cliente,
            'total' => $pedido->total,
            'formaDePago' => $pedido->formaDePago,
            'estado' => 1,
            'observaciones' => '',
            'anulada' => 0,
            'billTo' => optional($pedido->cliente)->direccionBill,
            'shipTo' => optional($pedido->cliente)->nombre,
            'shipVia' => '',
            'FOB' => '',
            'Terms' => '',
            'fechaOrden' => $pedido->fecha,
            'salesPerson' => $vendedorNombre,
            'orden' => $pedido->id,
            'peso' => 0,
            'cantidad' => $cantidad,
            'DescuentoNeto' => $pedido->DescuentoNeto,
            'DescuentoPorcentual' => $pedido->DescuentoPorcentual,
            'UPS' => '',
            'TotalEnvio' => $pedido->TotalEnvio,
            'codigoUPS' => $request->codigoUPS,
            'subTotal' => $subTotal,
            'DescuentoPorPromociones' => $pedido->DescuentoPromociones,
            'IdActiveCampaign' => 0,
        ];
    }
}
