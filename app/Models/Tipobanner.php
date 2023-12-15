<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tipobanner.
 *
 * @property int $id
 * @property string $palabraClave
 * @property string $nombre
 * @property string $descripcion
 * @property int|null $alto
 * @property int|null $ancho
 * @property string|null $codigo
 */
class Tipobanner extends Model
{
    protected $table = 'tipobanners';
    public $timestamps = false;

    protected $fillable = [
        'palabraClave',
        'nombre',
        'descripcion',
        'alto',
        'ancho',
        'codigo',
    ];
}
