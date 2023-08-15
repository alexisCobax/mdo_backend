<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Proveedor
 *
 * @property int $id
 * @property string $nombre
 * @property string $direccion
 * @property int|null $ciudad
 * @property string|null $codigoPostal
 * @property string $telefono
 * @property string|null $movil
 * @property string|null $email
 * @property string|null $fax
 * @property string|null $contacto
 * @property string|null $transportadora
 * @property string|null $telefonoTransportadora
 * @property string|null $observaciones
 * @property string|null $formaDePago
 * @property bool|null $suspendido
 *
 * @package App\Models
 */
class Proveedor extends Model
{
    protected $table = 'proveedor';
    public $timestamps = false;

    protected $casts = [
        'ciudad' => 'int',
        'suspendido' => 'bool'
    ];

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'codigoPostal',
        'telefono',
        'movil',
        'email',
        'fax',
        'contacto',
        'transportadora',
        'telefonoTransportadora',
        'observaciones',
        'formaDePago',
        'suspendido'
    ];
}
