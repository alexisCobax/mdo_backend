<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Prospecto
 *
 * @property int $id
 * @property string|null $nombre
 * @property string|null $direccion
 * @property string|null $codigoPostal
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $fax
 * @property string|null $contacto
 * @property string|null $puestoContacto
 * @property string|null $transportadora
 * @property string|null $telefonoTransportadora
 * @property string|null $observaciones
 * @property string|null $web
 * @property string|null $direccionShape
 * @property string|null $direccionBill
 * @property string|null $ciudad
 * @property string|null $pais
 * @property Carbon|null $fecha
 * @property string|null $tipo
 *
 * @package App\Models
 */
class Prospecto extends Model
{
    protected $table = 'prospecto';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'fecha' => 'datetime'
    ];

    protected $fillable = [
        'id',
        'nombre',
        'direccion',
        'codigoPostal',
        'telefono',
        'email',
        'fax',
        'contacto',
        'puestoContacto',
        'transportadora',
        'telefonoTransportadora',
        'observaciones',
        'web',
        'direccionShape',
        'direccionBill',
        'ciudad',
        'pais',
        'fecha',
        'tipo'
    ];
}
