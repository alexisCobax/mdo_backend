<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Movimientoproducto.
 *
 * @property int $id
 * @property Carbon $fecha
 * @property int $origen
 * @property int $destino
 * @property int $cantidad
 * @property int $idProducto
 * @property string $comentarios
 */
class Movimientoproducto extends Model
{
    protected $table = 'movimientoproductos';
    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime',
        'origen' => 'int',
        'destino' => 'int',
        'cantidad' => 'int',
        'idProducto' => 'int',
    ];

    protected $fillable = [
        'fecha',
        'origen',
        'destino',
        'cantidad',
        'idProducto',
        'comentarios',
    ];
}
