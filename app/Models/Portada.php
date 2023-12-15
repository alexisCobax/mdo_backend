<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Portada.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $visible
 * @property int $marca
 * @property int $posicion
 */
class Portada extends Model
{
    protected $table = 'portada';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'visible',
        'marca',
        'posicion',
    ];
}
