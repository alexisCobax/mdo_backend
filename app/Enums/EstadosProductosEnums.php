<?php

namespace App\Enums;

class EstadosProductosEnums
{
    public const SUSPENDIDO = 'Suspendido';
    public const PUBLICADO = 'Publicado';

    public static function toArray()
    {
        return [
            self::SUSPENDIDO => 1,
            self::PUBLICADO => 0,
        ];
    }
}
