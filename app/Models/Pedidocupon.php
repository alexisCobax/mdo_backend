<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedidocupon
 *
 * @property int $id
 * @property int|null $cupon
 * @property int|null $pedido
 * @property float|null $monto
 * @property int|null $cotizacion
 *
 * @package App\Models
 */
class Pedidocupon extends Model
{
    protected $table = 'pedidocupon';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'cupon' => 'int',
        'pedido' => 'int',
        'monto' => 'float',
        'cotizacion' => 'int'
    ];

    protected $fillable = [
        'id',
        'cupon',
        'pedido',
        'monto',
        'cotizacion'
    ];
}
