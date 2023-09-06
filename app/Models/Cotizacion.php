<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cotizacion.
 *
 * @property int $id
 * @property Carbon $fecha
 * @property int $cliente
 * @property float $total
 * @property int|null $estado
 * @property int|null $IdActiveCampaign
 * @property float|null $descuento
 * @property float|null $subTotal
 */
class Cotizacion extends Model
{
    protected $table = 'cotizacion';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'fecha' => 'datetime',
        'cliente' => 'int',
        'total' => 'float',
        'estado' => 'int',
        'IdActiveCampaign' => 'int',
        'descuento' => 'float',
        'subTotal' => 'float',
    ];

    protected $fillable = [
        'id',
        'fecha',
        'cliente',
        'total',
        'estado',
        'IdActiveCampaign',
        'descuento',
        'subTotal',
    ];

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente');
    }
}
