<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Paypal
 *
 * @property int $id
 * @property int|null $idPedido
 * @property string|null $token
 * @property string|null $respuesta
 * @property int $estado
 * @property string|null $respuestaFinal
 * @property int|null $idCotizacion
 *
 * @package App\Models
 */
class Paypal extends Model
{
    protected $table = 'paypal';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'idPedido' => 'int',
        'estado' => 'int',
        'idCotizacion' => 'int'
    ];

    protected $hidden = [
        'token'
    ];

    protected $fillable = [
        'id',
        'idPedido',
        'token',
        'respuesta',
        'estado',
        'respuestaFinal',
        'idCotizacion'
    ];
}
