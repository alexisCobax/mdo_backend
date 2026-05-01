<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Carritodetalle.
 *
 * @property int $id
 * @property int $carrito
 * @property int $producto
 * @property float $precio
 * @property int $cantidad
 * @property int $asesor
 */
class Carritodetalle extends Model
{
    protected $table = 'carritodetalle';
    public $timestamps = false;

    protected $fillable = [
        'carrito',
        'producto',
        'precio',
        'cantidad',
        'asesor'
    ];

    //Relationships

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'producto');
    }
}
