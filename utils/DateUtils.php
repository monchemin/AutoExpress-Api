<?php


namespace utils;


use DateTime;

class DateUtils
{
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function isValidDate($date) {
            if( self::validateDate($date) ) {
                $now = new DateTime();
                $mDate = new DateTime($date);
                return $now <= $mDate;
            } else {
                return false;
            }
    }

}