<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Sexoproducto.
 *
 * @property int $id
 * @property string $nombre
 */
class Sexoproducto extends Model
{
    protected $table = 'sexoproducto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
