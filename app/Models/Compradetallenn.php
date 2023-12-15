<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Compradetallenn.
 *
 * @property int $id
 * @property string $descripcion
 * @property float|null $precio
 * @property int $idCompra
 */
class Compradetallenn extends Model
{
    protected $table = 'compradetallenn';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'precio',
        'idCompra',
    ];
}
