<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recibo
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
 *
 * @package App\Models
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
        'garantia' => 'bool'
    ];

    protected $fillable = [
        'cliente',
        'fecha',
        'formaDePago',
        'total',
        'anulado',
        'observaciones',
        'pedido',
        'garantia'
    ];
}
