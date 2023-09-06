<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tipoproducto.
 *
 * @property int $id
 * @property string $nombre
 * @property int|null $CantidadMinima
 * @property bool $suspendido
 */
class Tipoproducto extends Model
{
    protected $table = 'tipoproducto';
    public $timestamps = false;

    protected $casts = [
        'CantidadMinima' => 'int',
        'suspendido' => 'bool',
    ];

    protected $fillable = [
        'nombre',
        'CantidadMinima',
        'suspendido',
    ];
}
