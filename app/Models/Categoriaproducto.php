<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Categoriaproducto.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 */
class Categoriaproducto extends Model
{
    protected $table = 'categoriaproducto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'suspendido',
    ];
}
