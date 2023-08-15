<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Carritodetalle
 *
 * @property int $id
 * @property int $carrito
 * @property int $producto
 * @property float $precio
 * @property int $cantidad
 *
 * @package App\Models
 */
class Carritodetalle extends Model
{
    protected $table = 'carritodetalle';
    public $timestamps = false;

    protected $casts = [
        'carrito' => 'int',
        'producto' => 'int',
        'precio' => 'float',
        'cantidad' => 'int'
    ];

    protected $fillable = [
        'carrito',
        'producto',
        'precio',
        'cantidad'
    ];

    //Relationships

    public function productos()
    {
        //return $this->belongsTo(Producto::class, 'producto');
        return $this->hasMany(Carritodetalle::class, 'producto');
    }
}
