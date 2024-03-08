<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cotizacion.
 *
 * @property int $id
 * @property Carbon $fecha
 * @property int $cliente
 * @property float $total
 * @property int|null $estado
 * @property int|null $IdActiveCampaign
 * @property float|null $descuento
 * @property float|null $subTotal
 */
class Cotizacion extends Model
{
    protected $table = 'cotizacion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'fecha',
        'cliente',
        'total',
        'estado',
        'IdActiveCampaign',
        'descuento',
        'subTotal',
    ];

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente');
    }

    public function scopeId($query, $id)
    {
        if ($id) {
            return $query->where('id', '=', $id);
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
            return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        return $query;
    }
}
