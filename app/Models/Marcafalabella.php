<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Marcafalabella.
 *
 * @property int $id
 * @property string $name
 * @property int $BrandId
 * @property string $GlobalIdentifier
 * @property string|null $Pais
 */
class Marcafalabella extends Model
{
    protected $table = 'marcafalabella';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'BrandId',
        'GlobalIdentifier',
        'Pais',
    ];
}
