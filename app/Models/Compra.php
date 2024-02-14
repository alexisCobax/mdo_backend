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

    public function scopeEnDeposito($query, $estado)
    {
        if ($estado == '0' or $estado == 1) {
            return $query->where('enDeposito', $estado);
        }

        return $query;
    }

    public function scopeProveedor($query, $proveedor)
    {
        if ($proveedor) {
            return $query->where('proveedor', $proveedor);
        }

        return $query;
    }

    public function scopeDesdeHasta($query, $fechaInicio, $fechaFin)
    {
        if ($fechaInicio or $fechaFin) {
            return $query->whereBetween('fechaDeIngreso', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        }

        return $query;
    }
}
