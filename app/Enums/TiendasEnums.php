<?php

namespace App\Enums;

class TiendasEnums
{
    public const MDO = 'mdo.net';
    public const JET = 'jet.com';
    public const FALABELLA = 'Falabella';

    public static function toArray()
    {
        return [
            self::MDO => '1',
            self::JET => '1',
            self::FALABELLA => '1',
        ];
    }
}
