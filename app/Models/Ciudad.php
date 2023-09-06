<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ciudad.
 *
 * @property int $id
 * @property string $pais
 * @property string $nombre
 */
class Ciudad extends Model
{
    protected $table = 'ciudad';
    public $timestamps = false;

    protected $fillable = [
        'pais',
        'nombre',
    ];
}
