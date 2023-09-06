<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Perfil.
 *
 * @property int $id
 * @property string $nombre
 */
class Perfil extends Model
{
    protected $table = 'perfil';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
