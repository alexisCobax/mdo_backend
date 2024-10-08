<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Fotoproducto.
 *
 * @property int $id
 * @property int $idProducto
 * @property int $orden
 */
class Fotoproducto extends Model
{
    protected $table = 'fotoproducto';
    public $timestamps = false;

    protected $fillable = [
        'idProducto',
        'orden',
        'url'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto');
    }
}
