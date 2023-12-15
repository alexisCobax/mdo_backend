<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Promocioncomprandoxgratisz.
 *
 * @property int $id
 * @property string $nombre
 * @property int $idMarca
 * @property int $Cantidad
 * @property int $CantidadBonificada
 * @property bool $activa
 */
class Promocioncomprandoxgratisz extends Model
{
    protected $table = 'promocioncomprandoxgratisz';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'idMarca',
        'Cantidad',
        'CantidadBonificada',
        'activa',
    ];
}
