<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Categoriafalabella.
 *
 * @property int $id
 * @property string $Name
 * @property int $CategoryId
 * @property string $GlobalIdentifier
 * @property int $AttributeSetId
 * @property int $PadreCategoryId
 * @property string|null $Pais
 */
class Categoriafalabella extends Model
{
    protected $table = 'categoriafalabella';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'Name',
        'CategoryId',
        'GlobalIdentifier',
        'AttributeSetId',
        'PadreCategoryId',
        'Pais',
    ];
}
