<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pais
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 *
 * @package App\Models
 */
class Pais extends Model
{
    protected $table = 'pais';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre'
    ];
}
