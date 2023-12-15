<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Color.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 */
class Color extends Model
{
    protected $table = 'color';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'suspendido',
    ];
}
