<?php

namespace App\Enums;

class HorasEnums
{
    public const DESDE = 'desde';
    public const HASTA = 'hasta';

    public static function toArray()
    {
        return [
            self::DESDE => '00:00:00',
            self::HASTA => '23:59:59',
        ];
    }
}
