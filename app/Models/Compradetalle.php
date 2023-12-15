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

    public function scopeEnDeposito($query, $estado)
    {
        if ($estado == '0' or $estado == 1) {
            return $query->where('enDeposito', $estado);
        }

        return $query;
    }

    public function scopeCompra($query, $compra)
    {
        if ($compra) {
            return $query->where('compra', $compra);
        }

        return $query;
    }
}
