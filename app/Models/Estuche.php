<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Estuche.
 *
 * @property int $id
 * @property string $nombre
 */
class Estuche extends Model
{
    protected $table = 'estuche';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
    ];
}
