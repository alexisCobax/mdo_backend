<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Color
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 *
 * @package App\Models
 */
class Color extends Model
{
    protected $table = 'color';
    public $timestamps = false;

    protected $casts = [
        'suspendido' => 'bool'
    ];

    protected $fillable = [
        'nombre',
        'suspendido'
    ];
}
