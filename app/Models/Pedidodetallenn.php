<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedidodetallenn.
 *
 * @property int $id
 * @property string|null $descripcion
 * @property float $precio
 * @property int $pedido
 * @property int $cantidad
 */
class Pedidodetallenn extends Model
{
    protected $table = 'pedidodetallenn';
    public $timestamps = false;

    protected $casts = [
        'precio' => 'float',
        'pedido' => 'int',
        'cantidad' => 'int',
    ];

    protected $fillable = [
        'descripcion',
        'precio',
        'pedido',
        'cantidad',
    ];
}
