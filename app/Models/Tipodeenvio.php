<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tipodeenvio.
 *
 * @property int $id
 * @property string $nombre
 */
class Tipodeenvio extends Model
{
    protected $table = 'tipodeenvio';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
    ];
}
