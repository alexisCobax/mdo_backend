<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cotizaciondetalle.
 *
 * @property int $id
 * @property int $cotizacion
 * @property int $producto
 * @property float $precio
 * @property int $cantidad
 */
class Cotizaciondetalle extends Model
{
    protected $table = 'cotizaciondetalle';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'cotizacion' => 'int',
        'producto' => 'int',
        'precio' => 'float',
        'cantidad' => 'int',
    ];

    protected $fillable = [
        'id',
        'cotizacion',
        'producto',
        'precio',
        'cantidad',
    ];

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'producto');
    }
}
