<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Plataformaproducto
 *
 * @property int $idProducto
 * @property int $idPlataforma
 *
 * @package App\Models
 */
class Plataformaproducto extends Model
{
    protected $table = 'plataformaproducto';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'idProducto' => 'int',
        'idPlataforma' => 'int'
    ];
}
