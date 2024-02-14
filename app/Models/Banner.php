<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Banner.
 *
 * @property int $id
 * @property int $tipoUbicacion
 * @property string $codigo
 * @property bool $suspendido
 * @property int|null $orden
 * @property string|null $tipoArchivo
 * @property string|null $link
 * @property string|null $nombre
 * @property string|null $tipo
 * @property string|null $texto1
 * @property string|null $texto2
 */
class Banner extends Model
{
    protected $table = 'banners';
    public $timestamps = false;

    protected $fillable = [
        'tipoUbicacion',
        'codigo',
        'suspendido',
        'orden',
        'tipoArchivo',
        'link',
        'nombre',
        'tipo',
        'texto1',
        'texto2',
    ];

    public function Ubicaciones()
    {
        return $this->belongsTo(Tipobanner::class, 'tipoUbicacion');
    }
}
