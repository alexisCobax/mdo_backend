<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class PaisOrdenado extends Pais
{
    protected $table = 'pais';

    public static function booted()
    {
        static::addGlobalScope('ordenPorNombre', function (Builder $builder) {
            $builder->orderBy('nombre', 'asc');
        });
    }
}
