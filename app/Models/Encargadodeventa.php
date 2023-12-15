<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Encargadodeventa.
 *
 * @property int $id
 * @property string $nombre
 * @property string $iniciales
 * @property bool $suspendido
 */
class Encargadodeventa extends Model
{
    protected $table = 'encargadodeventa';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'iniciales',
        'suspendido',
    ];
}
