<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Grupo.
 *
 * @property int $id
 * @property string $nombre
 */
class Grupo extends Model
{
    protected $table = 'grupo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
