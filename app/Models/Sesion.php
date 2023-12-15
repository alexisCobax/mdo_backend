<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Sesion.
 *
 * @property int $id
 * @property string $session
 * @property int $usuario
 */
class Sesion extends Model
{
    protected $table = 'sesion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'session',
        'usuario',
    ];
}
