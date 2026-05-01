<?php

/**
 * Modelo para la tabla permisos (catálogo de perfiles/permisos de usuario).
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Permiso.
 *
 * @property int $id
 * @property string $nombre
 */
class Permiso extends Model
{
    protected $table = 'permiso';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
