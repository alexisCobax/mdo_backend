<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Puesto
 *
 * @property int $id
 * @property string $nombre
 *
 * @package App\Models
 */
class Puesto extends Model
{
    protected $table = 'puesto';
    public $timestamps = false;

    protected $fillable = [
        'nombre'
    ];
}
