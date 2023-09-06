<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedidodetalle.
 *
 * @property int $id
 * @property int $pedido
 * @property int $producto
 * @property float $precio
 * @property int $cantidad
 * @property float|null $costo
 * @property float|null $envio
 * @property float|null $tax
 * @property float|null $taxEnvio
 * @property string|null $jet_order_item_id
 */
class Pedidodetalle extends Model
{
    protected $table = 'pedidodetalle';
    public $timestamps = false;

    protected $casts = [
        'pedido' => 'int',
        'producto' => 'int',
        'precio' => 'float',
        'cantidad' => 'int',
        'costo' => 'float',
        'envio' => 'float',
        'tax' => 'float',
        'taxEnvio' => 'float',
    ];

    protected $fillable = [
        'pedido',
        'producto',
        'precio',
        'cantidad',
        'costo',
        'envio',
        'tax',
        'taxEnvio',
        'jet_order_item_id',
    ];

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'producto');
    }
}
