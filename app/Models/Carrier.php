<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Carrier.
 *
 * @property int $id
 * @property string|null $nombre
 */
class Carrier extends Model
{
    protected $table = 'carrier';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'id',
        'nombre',
    ];
}
