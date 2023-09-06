<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Clientecontacto.
 *
 * @property int $id
 * @property string $nombre
 * @property string $telefono
 * @property string $email
 * @property Carbon|null $fechaNacimiento
 * @property string $puesto
 * @property string $comentarios
 * @property int $idCliente
 */
class Clientecontacto extends Model
{
    protected $table = 'clientecontacto';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'fechaNacimiento' => 'datetime',
        'idCliente' => 'int',
    ];

    protected $fillable = [
        'id',
        'nombre',
        'telefono',
        'email',
        'fechaNacimiento',
        'puesto',
        'comentarios',
        'idCliente',
    ];
}
