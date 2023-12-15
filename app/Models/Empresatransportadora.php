<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Empresatransportadora.
 *
 * @property int $id
 * @property string|null $nombre
 * @property string|null $ArchivoMail
 */
class Empresatransportadora extends Model
{
    protected $table = 'empresatransportadora';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'ArchivoMail',
    ];
}
