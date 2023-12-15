<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Marcaproducto.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $propia
 * @property bool $VIP
 * @property string $logo
 * @property bool|null $MostrarEnWeb
 * @property bool $suspendido
 */
class Marcaproducto extends Model
{
    protected $table = 'marcaproducto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'propia',
        'VIP',
        'logo',
        'MostrarEnWeb',
        'suspendido',
    ];
}
