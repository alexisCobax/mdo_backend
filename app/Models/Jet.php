<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Jet.
 *
 * @property int $id
 * @property string $token
 * @property Carbon $vencimiento
 * @property string $tokenType
 */
class Jet extends Model
{
    protected $table = 'jet';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'vencimiento' => 'datetime',
    ];

    protected $hidden = [
        'token',
    ];

    protected $fillable = [
        'id',
        'token',
        'vencimiento',
        'tokenType',
    ];
}
