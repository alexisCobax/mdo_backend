<?php

namespace App\Transformers\Invoices;

use App\Models\Pais;
use App\Models\Cliente;
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

        $cliente = Cliente::where('id', $pedido->cliente)->first();

        $pais = Pais::where('id', $pedido->paisEnvio)->first();

        $NombrePais = "";
        if(isset($pais->nombre )){$NombrePais= $pais->nombre ;}

        return [
            'cliente' => $pedido->cliente,
            'total' => $pedido->total,
            'formaDePago' => $pedido->formaDePago,
            'estado' => 1,
            'observaciones' => '',
            'anulada' => 0,
            'billTo' => $cliente->direccionBill,
            'shipTo' => $pedido->nombreEnvio . "\n" . $pedido->domicilioEnvio . "\n" . $pedido->ciudadEnvio . "\n" . $pedido->regionEnvio . "\n" . $NombrePais  . "\n" . 'ZIP: ' .$pedido->cpEnvio, // Envío | Cliente
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
