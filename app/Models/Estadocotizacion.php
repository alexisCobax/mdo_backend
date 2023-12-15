<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Estadocotizacion.
 *
 * @property int $id
 * @property string $nombre
 */
class Estadocotizacion extends Model
{
    protected $table = 'estadocotizacion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
    ];
}
