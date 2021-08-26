<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use Carbon\Carbon;

class DateHelper
{
    const DEFAULT_FORMATTER = 'Y-m-d';

    // 初始化时区
    public static function initTimezone()
    {
        $timezone = 'UTC + 8';
        //  $timezone = "UTC";
        config(['app.timezone' =>  $timezone]);
        // (UTC8 => Asia/Shanghai)
        date_default_timezone_set('Europe/Brussels');
        date_default_timezone_set('Asia/Shanghai');
    }

    // 毫秒
    public static function milliseconds($format = 'u', $utimestamp = null)
    {
        if (is_null($utimestamp)) {
            $utimestamp = microtime(true);
        }
        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000); //改这里的数值控制毫秒位数

        return $milliseconds;
    }

    // 毫秒
    public static function format_date($time)
    {
        $t = time() - $time;
        $f = [
            '31536000'=>' year ',
            '2592000'=>' month ',
            '604800'=>' week ',
            '86400'=>' day ',
            '3600'=>' hour ',
            '60'=>' min ',
            '1'=>' second ',
        ];
        foreach ($f as $k=>$v) {
            if (0 != $c = floor($t / (int) $k)) {
                return $c.$v.'前';
            }
        }
    }

    // 毫秒
    public static function format_date_zh($time)
    {
        $t = time() - $time;
        $f = [
            '31536000'=>' 年 ',
            '2592000'=>' 月 ',
            '604800'=>' 周 ',
            '86400'=>' 天 ',
            '3600'=>' 小时 ',
            '60'=>' 分钟 ',
            '1'=>' 秒 ',
        ];
        foreach ($f as $k=>$v) {
            if (0 != $c = floor($t / (int) $k)) {
                return $c.$v.'前';
            }
        }
    }

    // 根据时区获取时间
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

    // 获取小时
    public static function getHour($date)
    {
        return date('H', strtotime($date));
    }

    // 获取日期
    public static function getDate($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    // 获取月份
    public static function getMonth($date)
    {
        return date('Y-m', strtotime($date));
    }

    // 当前日期
    public static function currentDay($format = 'Y-m-d')
    {
        return date($format, time());
    }

    // 当前日期
    public static function currentTime($format = 'Y-m-d H:i:s')
    {
        return date($format, time());
    }

    // 当前日期
    public static function formatTime($t, $format = 'Y-m-d')
    {
        return date($format, $t);
    }

    public static function humanReadForSecond($sec)
    {
        if ($sec < 3600 && $sec >= 60) {
            return intval($sec / 60).' 分';
        }

        if ($sec > 3600) {
            return sprintf('%.2f', $sec / 3600).' 小时';
        }

        return $sec.' 秒';
    }

    // 2020-10-01, 7   =>  2020-10-07
    public static function addDays($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addDays($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    // 2020-10-01, 1   =>  2020-10-08
    public static function addWeeks($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addWeek($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    // 2020-10-01, 1   =>  2020-10-08
    public static function addMonth($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addMonth($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    // 2020-10-01, 1   =>  2020-10-08
    public static function addYears($fromDate, $n)
    {
        $carbon = new Carbon();
        $date = $carbon->setTimeFromTimeString($fromDate.' 00:00:00')->addYear($n)->format(self::DEFAULT_FORMATTER);

        return $date;
    }

    /**
     * 通过时区将传入的时间转换为数据库时间
     * time - 传入的时区时间.
     */
    public static function timezoneToAsiaShanghai($time)
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
     * 通过时区将数据库时间转换为该时区时间
     * time - 传入的本地时间.
     */
    public static function asiaShanghaiToTimezone($time)
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

    // 毫秒
    public static function format_date_langTag($time)
    {
        if (empty($time)) {
            return $time;
        }
        $langTag = request()->header('langTag');
        $language = ApiConfigHelper::getConfigByItemKey('language_menus');
        $langTag = ApiLanguageHelper::getLangTagByHeader();
        $langInfo = [];
        foreach ($language as $l) {
            if ($l['langTag'] == $langTag) {
                $langInfo = $l;
            }
        }
        // dd($langInfo);
        if (empty($langInfo)) {
            return '';
        }
        $t = time() - $time;
        $f = [
            '2592000'=>'month',
            '86400'=>'day',
            '3600'=>'hour',
            '60'=>'minute',
        ];
        foreach ($f as $k=>$v) {
            if (0 != $c = floor($t / (int) $k)) {
                // return $c.$v.'前';
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
