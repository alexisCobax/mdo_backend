<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\HorasEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedido.
 *
 * @property int $id
 * @property Carbon $fecha
 * @property int $cliente
 * @property int $estado
 * @property int $vendedor
 * @property int $formaDePago
 * @property string|null $observaciones
 * @property int|null $invoice
 * @property float|null $total
 * @property float|null $descuentoPorcentual
 * @property float|null $descuentoNeto
 * @property float|null $totalEnvio
 * @property int|null $recibo
 * @property int|null $origen
 * @property int|null $etapa
 * @property int|null $tipoDeEnvio
 * @property string|null $envioNombre
 * @property string|null $envioPais
 * @property string|null $envioRegion
 * @property string|null $envioCiudad
 * @property string|null $envioDomicilio
 * @property string|null $envioCp
 * @property string|null $idAgile
 * @property int|null $IdActiveCampaign
 * @property int|null $idTransportadora
 * @property string|null $transportadoraNombre
 * @property string|null $transportadoraTelefono
 * @property string|null $codigoSeguimiento
 * @property bool|null $MailSeguimientoEnviado
 */
class Pedido extends Model
{
    protected $table = 'pedido';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'cliente',
        'estado',
        'vendedor',
        'formaDePago',
        'observaciones',
        'invoice',
        'total',
        'descuentoPorcentual',
        'descuentoPromociones',
        'descuentoNeto',
        'totalEnvio',
        'recibo',
        'origen',
        'etapa',
        'tipoDeEnvio',
        'envioNombre',
        'envioPais',
        'envioRegion',
        'envioCiudad',
        'envioDomicilio',
        'envioCp',
        'idAgile',
        'IdActiveCampaign',
        'idTransportadora',
        'transportadoraNombre',
        'transportadoraTelefono',
        'codigoSeguimiento',
        'MailSeguimientoEnviado',
    ];

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente');
    }

    public function estadoPedido()
    {
        return $this->belongsTo(Estadopedido::class, 'estado');
    }

    public function formaDePagos()
    {
        return $this->belongsTo(Formadepago::class, 'formaDePago');
    }

    public function origenes()
    {
        return $this->belongsTo(Origenpedido::class, 'origen');
    }

    public function etapas()
    {
        return $this->belongsTo(Etapapedido::class, 'etapa');
    }

    public function tipoDeEnvios()
    {
        return $this->belongsTo(Tipodeenvio::class, 'tipoDeEnvio');
    }

    public function vendedores()
    {
        return $this->belongsTo(Encargadodeventa::class, 'vendedor');
    }

    //Filters

    public function scopeCodigo($query, $codigo)
    {
        if ($codigo) {
            return $query->where('id', '=', $codigo);
        }

        return $query;
    }

    public function scopeNombreCliente($query, $nombreCliente)
    {
        if ($nombreCliente) {
            return $query->whereHas('clientes', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'like', '%' . $nombreCliente . '%');
            });
        }

        return $query;
    }

    public function scopeEstado($query, $estado)
    {
        if ($estado) {
            return $query->where('estado', '=', $estado);
        }

        return $query;
    }

    public function scopeStockRange($query, $stockDesde, $stockHasta)
    {
        $horas = HorasEnums::toArray();

        if ($stockDesde !== null && $stockHasta !== null) {
            $stockDesde = $stockDesde . ' ' . $horas['desde'];
            $stockHasta = $stockHasta . ' ' . $horas['hasta'];

            return $query->whereBetween('fecha', [$stockDesde, $stockHasta]);
        } elseif ($stockDesde !== null) {
            $stockDesde = $stockDesde . ' ' . $horas['desde'];

            return $query->where('fecha', '>=', $stockDesde);
        } elseif ($stockHasta !== null) {
            $stockHasta = $stockHasta . ' ' . $horas['hasta'];

            return $query->where('fecha', '<=', $stockHasta);
        }

        return $query;
    }
}
