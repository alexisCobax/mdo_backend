<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Zipcode.
 *
 * @property int $id
 * @property string $zip
 * @property string $ciudad
 * @property float|null $precio
 */
class Zipcode extends Model
{
    protected $table = 'zipcodes';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'precio' => 'float',
    ];

    protected $fillable = [
        'id',
        'zip',
        'ciudad',
        'precio',
    ];
}
