<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use App\Models\Formadepago;
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

    protected $casts = [
        'cliente' => 'int',
        'fecha' => 'datetime',
        'formaDePago' => 'int',
        'total' => 'float',
        'anulado' => 'bool',
        'pedido' => 'int',
        'garantia' => 'bool',
    ];

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
}
