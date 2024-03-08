<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice.
 *
 * @property int $id
 * @property Carbon $fecha
 * @property int $cliente
 * @property float|null $total
 * @property int $formaDePago
 * @property int $estado
 * @property string $observaciones
 * @property bool $anulada
 * @property string $billTo
 * @property string $shipTo
 * @property string $shipVia
 * @property string $FOB
 * @property string $Terms
 * @property Carbon $fechaOrden
 * @property string $salesPerson
 * @property int $orden
 * @property float|null $peso
 * @property int $cantidad
 * @property float|null $DescuentoNeto
 * @property float|null $DescuentoPorcentual
 * @property string|null $UPS
 * @property float|null $TotalEnvio
 * @property string|null $codigoUPS
 * @property float|null $subTotal
 * @property float $DescuentoPorPromociones
 * @property int|null $IdActiveCampaign
 */
class Invoice extends Model
{
    protected $table = 'invoice';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'fecha',
        'cliente',
        'total',
        'formaDePago',
        'estado',
        'observaciones',
        'anulada',
        'billTo',
        'shipTo',
        'shipVia',
        'FOB',
        'Terms',
        'fechaOrden',
        'salesPerson',
        'orden',
        'peso',
        'cantidad',
        'DescuentoNeto',
        'DescuentoPorcentual',
        'UPS',
        'TotalEnvio',
        'codigoUPS',
        'subTotal',
        'DescuentoPorPromociones',
        'IdActiveCampaign',
    ];

    //Relationships

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente');
    }

    public function scopeCodigo($query, $codigo)
    {
        if ($codigo) {
            return $query->where('id', '=', $codigo);
        }

        return $query;
    }

    public function scopeClienteFiltro($query, $nombre)
    {
        if($nombre){
        $cliente = Cliente::where('nombre', 'like', '%' . $nombre . '%')->first();
        if ($cliente) {
            return $query->where('cliente', '=', $cliente->id);
        }
    }
        return $query;
    }

    public function scopeDesdeHasta($query, $fechaInicio, $fechaFin)
    {
        if ($fechaInicio or $fechaFin) {
            return $query->whereBetween('fechaOrden', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        }

        return $query;
    }
}
