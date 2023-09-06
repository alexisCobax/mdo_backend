<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cupondescuento.
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property float|null $descuentoFijo
 * @property float|null $descuentoPorcentual
 * @property int|null $marca
 * @property int|null $producto
 * @property int|null $cantidadMinima
 * @property float|null $montoMinimo
 * @property Carbon|null $vencimiento
 * @property int|null $stock
 * @property bool|null $suspendido
 * @property int|null $cantidadUtilizados
 * @property Carbon|null $inicio
 * @property bool|null $combinable
 */
class Cupondescuento extends Model
{
    protected $table = 'cupondescuento';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'descuentoFijo' => 'float',
        'descuentoPorcentual' => 'float',
        'marca' => 'int',
        'producto' => 'int',
        'cantidadMinima' => 'int',
        'montoMinimo' => 'float',
        'vencimiento' => 'datetime',
        'stock' => 'int',
        'suspendido' => 'bool',
        'cantidadUtilizados' => 'int',
        'inicio' => 'datetime',
        'combinable' => 'bool',
    ];

    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'descuentoFijo',
        'descuentoPorcentual',
        'marca',
        'producto',
        'cantidadMinima',
        'montoMinimo',
        'vencimiento',
        'stock',
        'suspendido',
        'cantidadUtilizados',
        'inicio',
        'combinable',
    ];
}
