<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Materialproducto.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 */
class Materialproducto extends Model
{
    protected $table = 'materialproducto';
    public $timestamps = false;

    protected $casts = [
        'suspendido' => 'bool',
    ];

    protected $fillable = [
        'nombre',
        'suspendido',
    ];
}
