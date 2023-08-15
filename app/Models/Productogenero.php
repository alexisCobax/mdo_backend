<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Productogenero
 *
 * @property int $id
 * @property string $nombre
 *
 * @package App\Models
 */
class Productogenero extends Model
{
    protected $table = 'productogenero';
    public $timestamps = false;

    protected $fillable = [
        'nombre'
    ];
}
