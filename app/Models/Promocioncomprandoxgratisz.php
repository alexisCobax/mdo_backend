<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Promocioncomprandoxgratisz
 *
 * @property int $id
 * @property string $nombre
 * @property int $idMarca
 * @property int $Cantidad
 * @property int $CantidadBonificada
 * @property bool $activa
 *
 * @package App\Models
 */
class Promocioncomprandoxgratisz extends Model
{
    protected $table = 'promocioncomprandoxgratisz';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'idMarca' => 'int',
        'Cantidad' => 'int',
        'CantidadBonificada' => 'int',
        'activa' => 'bool'
    ];

    protected $fillable = [
        'id',
        'nombre',
        'idMarca',
        'Cantidad',
        'CantidadBonificada',
        'activa'
    ];
}
