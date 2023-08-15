<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Sesion
 *
 * @property int $id
 * @property string $session
 * @property int $usuario
 *
 * @package App\Models
 */
class Sesion extends Model
{
    protected $table = 'sesion';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'usuario' => 'int'
    ];

    protected $fillable = [
        'id',
        'session',
        'usuario'
    ];
}
