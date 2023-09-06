<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subidasfalabella.
 *
 * @property int $id
 * @property Carbon|null $fecha
 * @property string|null $accion
 * @property int|null $idProducto
 * @property string|null $resultado
 * @property string|null $feed
 * @property string|null $pais
 */
class Subidasfalabella extends Model
{
    protected $table = 'subidasfalabella';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'fecha' => 'datetime',
        'idProducto' => 'int',
    ];

    protected $fillable = [
        'id',
        'fecha',
        'accion',
        'idProducto',
        'resultado',
        'feed',
        'pais',
    ];
}
