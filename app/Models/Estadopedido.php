<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Estadopedido.
 *
 * @property int $id
 * @property string $nombre
 */
class Estadopedido extends Model
{
    protected $table = 'estadopedido';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
