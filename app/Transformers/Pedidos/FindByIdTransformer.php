<?php

namespace App\Transformers\Pedidos;

use App\Models\Pedido;
use App\Models\Producto;
use App\Helpers\DateHelper;
use App\Models\Pedidodetalle;
use App\Models\Pedidodetallenn;
use League\Fractal\TransformerAbstract;
use App\Models\Pedidodescuentospromocion;

class FindByIdTransformer extends TransformerAbstract
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function transform()
    {

        $pedido = Pedido::find($this->request->id);

        $pedidoDetalle = Pedidodetalle::where('pedido', $this->request->id)->get();
        $detalle = [];

        foreach ($pedidoDetalle as $p) {

            $detalle[] = array(
                "id" => $p->id,
                "pedido" => $p->pedido,
                "producto" => $p->producto,
                "productoNombre" => $p->productos->nombre ?? "",
                "precio" => $p->precio,
                "cantidad" => $p->cantidad,
                "costo" => $p->costo,
                "envio" => $p->envio,
                "tax" => $p->tax,
                "taxEnvio" => $p->taxEnvio,
                "jet_order_item_id" => $p->jet_order_item_id
            );

        }

        $compraDetallenn = Pedidodetallenn::where('pedido', $this->request->id)->get();
        $detalleNN = [];

        foreach ($compraDetallenn as $pd) {
            $detalleNN[] = array(
                "id" => $pd->id,
                "descripcion" => $pd->descripcion,
                "precio" => $pd->precio,
                "pedido" => $pd->pedido,
                "cantidad" => $pd->cantidad
            );
        }

        $pedidoDescuentoPromocion = Pedidodescuentospromocion::where('idPedido', $this->request->id)->get();
        $descuento = [];

        foreach ($pedidoDescuentoPromocion as $pd) {
            $descuento[] = array(
                "id" => $pd->id,
                "idPedido" => $pd->idPedido,
                "idPromocion" => $pd->idPromocion,
                "descripcion" => $pd->descripcion,
                "montoDescuento" => $pd->montoDescuento,
                "idTipoPromocion" => $pd->idTipoPromocion
            );
        }

        return [
            'id' => $pedido->id,
            'fecha' => DateHelper::ToDateCustom($pedido->fecha),
            'cliente' => $pedido->cliente,
            'nombreCliente' => optional($pedido->clientes)->nombre,
            'estado' => $pedido->estado,
            'nombreEstado' => optional($pedido->estadoPedido)->nombre,
            'vendedor' => $pedido->vendedor,
            'nombreEmpleado' => optional($pedido->vendedores)->nombre,
            'formaDePago' => $pedido->formaDePago,
            'nombreFormaDePago' => optional($pedido->formaDePagos)->nombre,
            'observaciones' => $pedido->observaciones,
            'invoice' => $pedido->invoice,
            'total' => $pedido->total,
            'descuentoPorcentual' => $pedido->descuentoPorcentual,
            'descuentoNeto' => $pedido->descuentoNeto,
            'totalEnvio' => $pedido->totalEnvio,
            'recibo' => $pedido->recibo,
            'origen' => $pedido->origen,
            'nombreOrigen' => optional($pedido->origenes)->nombre,
            'etapa' => $pedido->etapa,
            'nombreEtapa' => optional($pedido->etapas)->nombre,
            'tipoDeEnvio' => $pedido->tipoDeEnvio,
            'nombreTipoDeEnvio' =>  optional($pedido->tipoDeEnvios)->nombre,
            'envioNombre' => $pedido->envioNombre,
            'envioPais' => $pedido->envioPais,
            'envioRegion' => $pedido->envioRegion,
            'envioCiudad' => $pedido->envioCiudad,
            'envioDomicilio' => $pedido->envioDomicilio,
            'envioCp' => $pedido->envioCp,
            'idAgile' => $pedido->idAgile,
            'IdActiveCampaign' => $pedido->IdActiveCampaign,
            'transportadoraNombre' => $pedido->transportadoraNombre,
            'transportadoraTelefono' => $pedido->transportadoraTelefono,
            'codigoSeguimiento' => $pedido->codigoSeguimiento,
            'MailSeguimientoEnviado' => $pedido->MailSeguimientoEnviado,
            "detalle" => $detalle,
            "detalleNN"  => $detalleNN,
            "descuentosPromocion" => $descuento
        ];
    }
}
