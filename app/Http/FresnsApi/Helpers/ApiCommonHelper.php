<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Helpers;

use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsStopWords\FresnsStopWords;
use Illuminate\Support\Str;

class ApiCommonHelper
{
    // 是否https请求
    public static function isHttpsRequest()
    {
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            return true;
        }

        return false;
    }

    // 获取域名
    public static function domain()
    {
        $request = request();
        $httpHost = $request->server('HTTP_HOST');

        if (self::isHttpsRequest()) {
            return 'https://'.$httpHost;
        }

        return 'http://'.$httpHost;
    }

    // 电话加密
    public static function encryptPhone($phone, $start = 3, $end = 6)
    {
        if (empty($phone)) {
            return '';
        }

        return substr_replace($phone, '****', $start, $end);
    }

    // 邮箱加密
    public static function encryptEmail($email)
    {
        if (empty($email)) {
            return '';
        }
        $emailArr = explode('@', $email);

        $email = null;
        if ($emailArr) {
            $email1 = substr_replace($emailArr[0], '***', 3);
            if (empty($email1)) {
                return '';
            }
            $email = $email1.'@'.$emailArr[1];
        }

        return $email;
    }

    //姓名加密
    public static function encryptName($name)
    {
        $name = mb_substr($name, -1, 1);

        $name = '*'.$name;

        return $name;
    }

    // 身份证加密
    public static function encryptIdNumber($number, $startNum = 1, $endNum = 1)
    {
        $num = strlen($number);
        $count = $startNum + $endNum;
        $num = $num - $count;
        $str = '';
        $str = sprintf("%'*".$num.'s', $str);
        $start = mb_substr($number, 0, $startNum);
        $end = mb_substr($number, $endNum);

        return $start.$str.$end;
    }

    //生成uuid
    public static function createUuid($length = 8)
    {
        $str = Str::random($length);
        $str = strtolower($str);

        return $str;
    }

    //member的uuid 8位数字
    public static function createMemberUuid()
    {
        $uuid = rand(10000000, 99999999);

        //查询是否有重复的
        $count = FresnsMembers::where('uuid', $uuid)->count();
        if ($count > 0) {
            $uuid = rand(10000000, 99999999);
        }

        return $uuid;
    }

    // Stop Word Rules
    public static function stopWords($text)
    {
        $stopWordsArr = FresnsStopWords::get()->toArray();

        foreach ($stopWordsArr as $v) {
            $str = strstr($text, $v['word']);
            // dd($str);
            if ($str != false) {
                if ($v['dialog_mode'] == 2) {
                    $text = str_replace($v['word'], $v['replace_word'], $text);

                    return $text;
                }
                if ($v['dialog_mode'] == 3) {
                    return false;
                }
            }
        }

        return $text;
    }
}
