<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaccion.
 *
 * @property int $id
 * @property Carbon $fecha
 * @property int $cliente
 * @property int $pedido
 * @property string $resultado
 * @property string $ctr
 */
class Transaccion extends Model
{
    protected $table = 'transaccion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'fecha',
        'cliente',
        'pedido',
        'resultado',
        'ctr',
    ];
}
