<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tamanoproducto.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 */
class Tamanoproducto extends Model
{
    protected $table = 'tamanoproducto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'suspendido',
    ];
}
