<?php

namespace app\sys;

use DateTime;
use DateTimeZone;
use Throwable;

class system_datetime
{
    static $timeZone;

    static function setTime(string $timezone)
    {
        self::$timeZone = new DateTimeZone($timezone);
    }

    static function getTimeZoneName()
    {
        return self::$timeZone->getName();
    }

    static function getDate(string $dateFormat, int $time = null)
    {
        $datetime = new DateTime('now', self::$timeZone);
        if (isset($time)) $datetime->setTimestamp($time);
        return $datetime->format($dateFormat);
    }

    static function getTimeForStr(string $dateStr)
    {
        $datetime = new DateTime($dateStr, self::$timeZone);
        return $datetime->getTimestamp();
    }

    static function checkDataTimeFormat(string $dateStr, string $format = 'Y-m-d H:i:s')
    {
        try {
            $datetime = new DateTime($dateStr, self::$timeZone);
        } catch (Throwable $e) {
            return false;
        }
        return $dateStr == $datetime->format($format);
    }
}
