<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Etapapedido.
 *
 * @property int|null $id
 * @property string|null $nombre
 */
class Etapapedido extends Model
{
    protected $table = 'etapapedido';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
    ];
}
