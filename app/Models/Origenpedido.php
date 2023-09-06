<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Origenpedido.
 *
 * @property int $id
 * @property string $nombre
 */
class Origenpedido extends Model
{
    protected $table = 'origenpedido';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'id',
        'nombre',
    ];
}
