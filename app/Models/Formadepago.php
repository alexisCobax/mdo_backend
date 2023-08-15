<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Formadepago
 *
 * @property int $id
 * @property string $nombre
 *
 * @package App\Models
 */
class Formadepago extends Model
{
    protected $table = 'formadepago';
    public $timestamps = false;

    protected $fillable = [
        'nombre'
    ];
}
