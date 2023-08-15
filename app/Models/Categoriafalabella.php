<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Categoriafalabella
 *
 * @property int $id
 * @property string $Name
 * @property int $CategoryId
 * @property string $GlobalIdentifier
 * @property int $AttributeSetId
 * @property int $PadreCategoryId
 * @property string|null $Pais
 *
 * @package App\Models
 */
class Categoriafalabella extends Model
{
    protected $table = 'categoriafalabella';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'CategoryId' => 'int',
        'AttributeSetId' => 'int',
        'PadreCategoryId' => 'int'
    ];

    protected $fillable = [
        'id',
        'Name',
        'CategoryId',
        'GlobalIdentifier',
        'AttributeSetId',
        'PadreCategoryId',
        'Pais'
    ];
}
