<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedidocupon.
 *
 * @property int $id
 * @property int|null $cupon
 * @property int|null $pedido
 * @property float|null $monto
 * @property int|null $cotizacion
 */
class Pedidocupon extends Model
{
    protected $table = 'pedidocupon';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'cupon',
        'pedido',
        'monto',
        'cotizacion',
    ];
}
