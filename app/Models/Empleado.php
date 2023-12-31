<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Empleado.
 *
 * @property int $id
 * @property string $nombre
 * @property int $puesto
 * @property bool $suspendido
 * @property int $usuario
 * @property string $direccion
 * @property string $telefono
 * @property string $email
 * @property int $ciudad
 */
class Empleado extends Model
{
    protected $table = 'empleado';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'puesto',
        'suspendido',
        'usuario',
        'direccion',
        'telefono',
        'email',
        'ciudad',
    ];
}
