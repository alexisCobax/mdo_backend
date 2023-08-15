<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Portada
 *
 * @property int $id
 * @property string $nombre
 * @property bool $visible
 * @property int $marca
 * @property int $posicion
 *
 * @package App\Models
 */
class Portada extends Model
{
    protected $table = 'portada';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'visible' => 'bool',
        'marca' => 'int',
        'posicion' => 'int'
    ];

    protected $fillable = [
        'id',
        'nombre',
        'visible',
        'marca',
        'posicion'
    ];
}
