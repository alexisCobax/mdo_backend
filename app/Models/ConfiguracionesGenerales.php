<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfiguracionesGenerales.
 *
 * @property int $id
 * @property string $nombre
 * @property string $valor
 * @property int $estado
 */
class ConfiguracionesGenerales extends Model
{
    protected $table = 'configuracionesgenerales';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'valor',
        'estado'
    ];
}
