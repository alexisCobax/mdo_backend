<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Configuracion.
 *
 * @property int $id
 * @property string $variable
 * @property string $valor
 */
class Configuracion extends Model
{
    protected $table = 'configuracion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'variable',
        'valor',
    ];
}
