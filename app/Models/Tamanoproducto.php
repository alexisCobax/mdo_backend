<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tamanoproducto
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 *
 * @package App\Models
 */
class Tamanoproducto extends Model
{
    protected $table = 'tamanoproducto';
    public $timestamps = false;

    protected $casts = [
        'suspendido' => 'bool'
    ];

    protected $fillable = [
        'nombre',
        'suspendido'
    ];
}
