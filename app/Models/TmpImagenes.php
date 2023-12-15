<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Banner.
 */
class TmpImagenes extends Model
{
    protected $table = 'tmp_imagenes';
    public $timestamps = false;

    protected $fillable = [
        'imagen',
        'nombre',
    ];
}
