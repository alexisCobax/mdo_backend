<?php

namespace App\Enums;

class CondicionalesEnums
{
    public const SI = 'Si';
    public const NO = 'No';

    public static function toArray()
    {
        return [
            self::SI => 1,
            self::NO => 0
        ];
    }
}
