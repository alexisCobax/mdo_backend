<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente2.
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
 * @property int $usuario
 * @property bool $suspendido
 * @property string|null $web
 * @property string|null $direccionShape
 * @property string|null $direccionBill
 * @property int|null $vendedor
 * @property string|null $ciudad
 * @property string|null $pais
 * @property string|null $usuarioVIP
 * @property string|null $claveVIP
 * @property bool|null $VIP
 * @property float|null $ctacte
 * @property string|null $cpShape
 * @property string|null $paisShape
 * @property Carbon|null $primeraCompra
 * @property int|null $cantidadDeCompras
 * @property string|null $idAgile
 * @property float|null $montoMaximoDePago
 * @property string|null $WhatsApp
 * @property string|null $Notas
 * @property int|null $tipoDeEnvio
 * @property string|null $nombreEnvio
 * @property string|null $regionEnvio
 * @property string|null $ciudadEnvio
 * @property Carbon|null $fechaAlta
 * @property string|null $ipAlta
 * @property Carbon|null $ultimoLogin
 * @property string|null $ipUltimoLogin
 * @property bool|null $prospecto
 * @property string|null $contactoApellido
 */
class Cliente2 extends Model
{
    protected $table = 'cliente2';
    public $timestamps = false;

    protected $fillable = [
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
        'usuario',
        'suspendido',
        'web',
        'direccionShape',
        'direccionBill',
        'vendedor',
        'ciudad',
        'pais',
        'usuarioVIP',
        'claveVIP',
        'VIP',
        'ctacte',
        'cpShape',
        'paisShape',
        'primeraCompra',
        'cantidadDeCompras',
        'idAgile',
        'montoMaximoDePago',
        'WhatsApp',
        'Notas',
        'tipoDeEnvio',
        'nombreEnvio',
        'regionEnvio',
        'ciudadEnvio',
        'fechaAlta',
        'ipAlta',
        'ultimoLogin',
        'ipUltimoLogin',
        'prospecto',
        'contactoApellido',
    ];
}
