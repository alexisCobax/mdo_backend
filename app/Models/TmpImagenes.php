<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Banner
 *
 * @package App\Models
 */
class TmpImagenes extends Model
{
    protected $table = 'tmp_imagenes';
    public $timestamps = false;

    protected $casts = [];

    protected $fillable = [
        'imagen', 
        'nombre'
    ];
}
