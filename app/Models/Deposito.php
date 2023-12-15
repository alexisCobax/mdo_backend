<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Deposito.
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 */
class Deposito extends Model
{
    protected $table = 'deposito';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'suspendido',
    ];
}
