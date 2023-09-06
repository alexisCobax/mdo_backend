<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Plataforma.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 */
class Plataforma extends Model
{
    protected $table = 'plataforma';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'suspendido' => 'bool',
    ];

    protected $fillable = [
        'id',
        'nombre',
        'suspendido',
    ];
}
