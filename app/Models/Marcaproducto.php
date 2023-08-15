<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Marcaproducto
 *
 * @property int $id
 * @property string $nombre
 * @property bool $propia
 * @property bool $VIP
 * @property string $logo
 * @property bool|null $MostrarEnWeb
 * @property bool $suspendido
 *
 * @package App\Models
 */
class Marcaproducto extends Model
{
    protected $table = 'marcaproducto';
    public $timestamps = false;

    protected $casts = [
        'propia' => 'bool',
        'VIP' => 'bool',
        'MostrarEnWeb' => 'bool',
        'suspendido' => 'bool'
    ];

    protected $fillable = [
        'nombre',
        'propia',
        'VIP',
        'logo',
        'MostrarEnWeb',
        'suspendido'
    ];
}
