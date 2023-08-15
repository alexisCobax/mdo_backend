<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedidodescuentospromocion
 *
 * @property int $id
 * @property int $idPedido
 * @property int $idPromocion
 * @property string $descripcion
 * @property float $montoDescuento
 * @property int $idTipoPromocion
 *
 * @package App\Models
 */
class Pedidodescuentospromocion extends Model
{
    protected $table = 'pedidodescuentospromocion';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'idPedido' => 'int',
        'idPromocion' => 'int',
        'montoDescuento' => 'float',
        'idTipoPromocion' => 'int'
    ];

    protected $fillable = [
        'id',
        'idPedido',
        'idPromocion',
        'descripcion',
        'montoDescuento',
        'idTipoPromocion'
    ];
}
