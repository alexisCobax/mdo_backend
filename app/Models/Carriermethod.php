<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Carriermethod.
 *
 * @property int $id
 * @property string|null $nombre
 */
class Carriermethod extends Model
{
    protected $table = 'carriermethod';
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
