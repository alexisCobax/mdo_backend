<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reintegro.
 *
 * @property int $id
 * @property int $cliente
 * @property Carbon $fecha
 * @property float $total
 * @property bool $anulado
 * @property string $observaciones
 */
class Reintegro extends Model
{
    protected $table = 'reintegro';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'cliente' => 'int',
        'fecha' => 'datetime',
        'total' => 'float',
        'anulado' => 'bool',
    ];

    protected $fillable = [
        'id',
        'cliente',
        'fecha',
        'total',
        'anulado',
        'observaciones',
    ];
}
