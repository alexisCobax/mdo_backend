<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Banner.
 */
class TmpProductos extends Model
{
    protected $table = 'tmp_productos';
    public $timestamps = false;

    protected $casts = [
        'SKU' => 'int',
    ];

    protected $fillable = [
        'SKU',
        'marca',
        'nombre',
        'tipo',
        'color_fabricante',
        'color_generico',
        'tamaño',
        'material',
        'cantidad',
        'estuche',
        'costo',
        'precio_venta',
        'upc',
        'Image',
    ];
}
