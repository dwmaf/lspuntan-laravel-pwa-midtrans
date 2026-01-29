<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format date to Indonesian (e.g. 23 Desember 2029)
     *
     * @param mixed $date
     * @param string $format
     * @return string
     */
    public static function formatIdDate($date, $format = 'D MMMM Y')
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->locale('id')->isoFormat($format);
    }
}
