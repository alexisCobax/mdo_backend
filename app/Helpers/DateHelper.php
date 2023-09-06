<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * getDateCustom.
     *
     * @param  mixed $date
     * @return void
     *
     * Response Date Example 22-May-2023
     */
    public static function ToDateCustom($date)
    {
        return Carbon::parse($date)->format('d-M-Y');
    }
}
