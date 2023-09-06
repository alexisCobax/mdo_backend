<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Comision.
 *
 * @property int $id
 * @property string $nombre
 * @property int $porcentaje
 */
class Comision extends Model
{
    protected $table = 'comision';
    public $timestamps = false;

    protected $casts = [
        'porcentaje' => 'int',
    ];

    protected $fillable = [
        'nombre',
        'porcentaje',
    ];
}
