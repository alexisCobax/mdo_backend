<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Carrito.
 *
 * @property int $id
 * @property Carbon|null $fecha
 * @property int|null $cliente
 * @property int|null $estado
 * @property int|null $vendedor
 * @property int|null $formaDePago
 * @property string $session
 * @property string|null $observaciones
 */
class Carrito extends Model
{
    protected $table = 'carrito';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'fecha',
        'cliente',
        'estado',
        'vendedor',
        'formaDePago',
        'session',
        'observaciones',
    ];

    public function cupones()
    {
        return $this->belongsTo(Cupondescuento::class, 'cupon');
    }
}
