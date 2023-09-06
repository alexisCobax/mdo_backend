<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Compradetalle.
 *
 * @property int $id
 * @property int $compra
 * @property int $producto
 * @property int $cantidad
 * @property float|null $precioUnitario
 * @property bool $enDeposito
 */
class Compradetalle extends Model
{
    protected $table = 'compradetalle';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'compra' => 'int',
        'producto' => 'int',
        'cantidad' => 'int',
        'precioUnitario' => 'float',
        'enDeposito' => 'bool',
    ];

    protected $fillable = [
        'id',
        'compra',
        'producto',
        'cantidad',
        'precioUnitario',
        'enDeposito',
    ];

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'producto');
    }
}
