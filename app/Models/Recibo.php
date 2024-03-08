<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recibo.
 *
 * @property int $id
 * @property int $cliente
 * @property Carbon $fecha
 * @property int $formaDePago
 * @property float $total
 * @property bool $anulado
 * @property string $observaciones
 * @property int|null $pedido
 * @property bool $garantia
 */
class Recibo extends Model
{
    protected $table = 'recibo';
    public $timestamps = false;

    protected $fillable = [
        'cliente',
        'fecha',
        'formaDePago',
        'total',
        'anulado',
        'observaciones',
        'pedido',
        'garantia',
    ];

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente');
    }

    public function formasPago()
    {
        return $this->belongsTo(Formadepago::class, 'formaDePago');
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
