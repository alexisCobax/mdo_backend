<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Deposito
 *
 * @property int $id
 * @property string $nombre
 * @property bool $suspendido
 *
 * @package App\Models
 */
class Deposito extends Model
{
    protected $table = 'deposito';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'suspendido' => 'bool'
    ];

    protected $fillable = [
        'nombre',
        'suspendido'
    ];
}
