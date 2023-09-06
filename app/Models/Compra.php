<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Compra.
 *
 * @property int $id
 * @property int $proveedor
 * @property Carbon $fechaDeIngreso
 * @property Carbon $fechaDePago
 * @property float|null $precio
 * @property string $numeroLote
 * @property string|null $observaciones
 * @property bool $pagado
 * @property bool $enDeposito
 */
class Compra extends Model
{
    protected $table = 'compra';
    public $timestamps = false;

    protected $casts = [
        'proveedor' => 'int',
        'fechaDeIngreso' => 'datetime',
        'fechaDePago' => 'datetime',
        'precio' => 'float',
        'pagado' => 'bool',
        'enDeposito' => 'bool',
    ];

    protected $fillable = [
        'proveedor',
        'fechaDeIngreso',
        'fechaDePago',
        'precio',
        'numeroLote',
        'observaciones',
        'pagado',
        'enDeposito',
    ];

    public function proveedores()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor');
    }
}
