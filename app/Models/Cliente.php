<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente.
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
 * @property int|null $IdActiveCampaign
 * @property int|null $IdActiveCampaignContact
 * @property bool $notification
 */
class Cliente extends Model
{
    protected $table = 'cliente';
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
        'IdActiveCampaign',
        'IdActiveCampaignContact',
        'notification',
    ];

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario');
    }

    public function paises()
    {
        return $this->belongsTo(Pais::class, 'pais');
    }

    //Filtros

    public function scopeId($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', '%' . $nombre . '%');
    }

    public function scopeEmail($query, $email)
    {
        return $query->where('email', 'like', '%' . $email . '%');
    }

    public function scopeTelefono($query, $telefono)
    {
        return $query->where('telefono', '=', $telefono);
    }

    public function scopeContacto($query, $contacto)
    {
        return $query->where('contacto', 'like', '%' . $contacto . '%');
    }
}
