<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Usuario.
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $email
 * @property string $clave
 * @property int $permisos
 * @property bool $suspendido
 * @property string|null $token
 * @property Carbon|null $token_exp
 */
class Usuario extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    protected $table = 'usuario';
    public $timestamps = false;

    protected $hidden = [
        'token',
    ];

    protected $fillable = [
        'nombre',
        'clave',
        'permisos',
        'suspendido',
        'clave_old',
        'frase',
    ];

    public function scopePerfil($query, $perfil)
    {
        if ($perfil == 1) {
            return $query->where('permisos', 1); //usuarios
        }

        return $query;
    }
}
