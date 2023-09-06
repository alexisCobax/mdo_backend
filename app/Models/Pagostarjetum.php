<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pagostarjetum.
 *
 * @property int $id
 * @property int $idPedido
 * @property string $respuesta
 * @property string $CC
 * @property string $Vencimiento
 */
class Pagostarjetum extends Model
{
    protected $table = 'pagostarjeta';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'idPedido' => 'int',
    ];

    protected $fillable = [
        'id',
        'idPedido',
        'respuesta',
        'CC',
        'Vencimiento',
    ];
}
