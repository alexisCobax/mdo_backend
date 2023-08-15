<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Moneda
 *
 * @property int $id
 * @property string $NombreMoneda
 * @property string $Pais
 * @property float $Cotizacion
 *
 * @package App\Models
 */
class Moneda extends Model
{
    protected $table = 'monedas';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'Cotizacion' => 'float'
    ];

    protected $fillable = [
        'id',
        'NombreMoneda',
        'Pais',
        'Cotizacion'
    ];
}
