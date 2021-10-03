<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use Carbon\Carbon;

class DateHelper
{
    const DEFAULT_FORMATTER = 'Y-m-d';

    // Initialize time zone
    public static function initTimezone()
    {
        $timezone = 'UTC + 8';
        // $timezone = "UTC";
        config(['app.timezone' =>  $timezone]);
        // (UTC8 => Asia/Singapore)
        date_default_timezone_set('Europe/London');
        date_default_timezone_set('Asia/Singapore');
    }

    // Milliseconds
    public static function milliseconds($format = 'u', $utimestamp = null)
    {
        if (is_null($utimestamp)) {
            $utimestamp = microtime(true);
        }
        $timestamp = floor($utimestamp);

        //Change the value here to control the number of milliseconds
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return $milliseconds;
    }

    // format date
    public static function format_date($time)
    {
        $t = time() - $time;
        $f = [
            '31536000' => ' year ',
            '2592000' => ' month ',
            '604800' => ' week ',
            '86400' => ' day ',
            '3600' => ' hour ',
            '60' => ' min ',
            '1' => ' second ',
        ];
        foreach ($f as $k=>$v) {
            if (0 != $c = floor($t / (int) $k)) {
                return $c.$v.'ago';
            }
        }
    }

    // Get time according to time zone
    public static function exDate($format, $timeZone = null)
    {
        if ($timeZone === null) {
            $timeZone = date_default_timezone_get();
        }
        $dateTimeZone = new \DateTimeZone($timeZone);
        $dateTime = new \DateTime();
        $dateTime->setTimezone($dateTimeZone);

        return $dateTime->format($format);
    }

    // Get Hours
    public static function getHour($date)
    {
        return date('H', strtotime($date));
    }

    // Get Date
    public static function getDate($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    // Get Month
    public static function getMonth($date)
    {
        return date('Y-m', strtotime($date));
    }

    // Current Date
    public static function currentDay($format = 'Y-m-d')
    {
        return date($format, time());
    }

    // Current Date and Time
    public static function currentTime($format = 'Y-m-d H:i:s')
    {
        return date($format, time());
    }

    // Current Date
    public static function formatTime($t, $format = 'Y-m-d')
    {
        return date($format, $t);
    }

    public static function humanReadForSecond($sec)
    {
        if ($sec < 3600 && $sec >= 60) {
            return intval($sec / 60).' Month';
        }

        if ($sec > 3600) {
            return sprintf('%.2f', $sec / 3600).' Hour';
        }

        return $sec.' Second';
    }

    // add Days
    public static function addDays($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addDays($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    // add Weeks
    public static function addWeeks($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addWeek($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    // add Month
    public static function addMonth($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addMonth($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    // add Years
    public static function addYears($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addYear($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    /**
     * Convert incoming time to database time via time zone
     * time - Incoming time zone time.
     */
    public static function fresnsInputTimeToTimezone($time)
    {
        if (empty($time)) {
            return $time;
        }

        $timezone = request()->header('timezone');
        if (isset($timezone)) {
            $utcZone = (0 - $timezone) * 3600;
            $utcTime = date('Y-m-d H:i:s', strtotime($time) + $utcZone);
            $time = date('Y-m-d H:i:s', strtotime($utcTime) + 8 * 3600);
        }

        return $time;
    }

    /**
     * Convert database time to this time zone by time zone
     * time - Incoming local time.
     */
    public static function fresnsOutputTimeToTimezone($time)
    {
        if (empty($time)) {
            return $time;
        }

        $timezone = request()->header('timezone');
        if (isset($timezone)) {
            $utcZone = 8 * 3600;
            $utcTime = date('Y-m-d H:i:s', strtotime($time) - $utcZone);
            $time = date('Y-m-d H:i:s', strtotime($utcTime) + $timezone * 3600);
        }

        return $time;
    }

    // Humanization time
    public static function format_date_langTag($time)
    {
        if (empty($time)) {
            return $time;
        }
        $langTag = request()->header('langTag');
        $language = ApiConfigHelper::getConfigByItemKey('language_menus');
        $langTag = ApiLanguageHelper::getLangTagByHeader();
        $langInfo = [];
        if ($language) {
            $language = json_decode($language, true);
            foreach ($language as $l) {
                if ($l['langTag'] == $langTag) {
                    $langInfo = $l;
                }
            }
        }

        if (empty($langInfo)) {
            return '';
        }
        $t = time() - $time;
        $f = [
            '2592000' => 'month',
            '86400' => 'day',
            '3600' => 'hour',
            '60' => 'minute',
        ];
        foreach ($f as $k=>$v) {
            if (0 != $c = floor($t / (int) $k)) {
                // return $c.$v.'ago';
                if ($v == 'minute') {
                    return str_replace('{n}', $c, $langInfo['timeFormatMinute']);
                }
                if ($v == 'hour') {
                    return str_replace('{n}', $c, $langInfo['timeFormatHour']);
                }
                if ($v == 'day') {
                    return str_replace('{n}', $c, $langInfo['timeFormatDay']);
                }
                if ($v == 'month') {
                    return str_replace('{n}', $c, $langInfo['timeFormatMonth']);
                }
            }
        }
    }
}
