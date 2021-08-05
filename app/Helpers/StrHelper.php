<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Http\Share\Common\LogService;
use App\Plugins\Share\Area\Area;
use Illuminate\Support\Str;

class StrHelper
{
    // 随机字符串
    public static function randString($length = 10){
        return Str::random($length);
    }

    // 随机字符串
    public static function randOrderNo($prefix = "BD"){
        $t = date("YmdHis", time());
        return $prefix . $t . rand(100,999) . rand(10000, 99999);
    }

    //去除字符串中所有中文字符
    public static function replaceZh($str){
        $str = preg_replace('/([\x80-\xff]*)/i','',$str);

        return $str;
    }

    // 判断是否为 true
    public static function isTrue($val, $return_null=false){
        $boolVal = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolVal===null && !$return_null ? false : $boolVal );
    }

    public static function randSmsCode($length = 6){
        $smsCode = rand(100000, 999999);
        return $smsCode;
    }

    // 创建token
    public static function createToken($length = 30){
        return Str::random($length);
    }

    public static function createPassword($str){
        return password_hash($str, PASSWORD_BCRYPT);
    }

    public static function createPhone($phone){
        return substr_replace($phone,'****',3,4);
    }

    // 电话加密
    public static function encryptPhone($phone){
        return substr_replace($phone,'****',3,4);
    }

    // 身份证加密
    public static function encryptIdNumber($number){
        return substr_replace($number,'********',4,11);
    }

    //分变元
    public static function createYuan($price,$count = 2){
        return sprintf("%." . $count . "f",$price / 100);
    }

    // 签名联系qr
    public static function signConcatQrData($phone)
    {
        $src = $phone . "," . date("Ymd") . "," . (date("His")+24*60*60);
        $keys = [0xA1, 0xB7, 0xAC, 0x57, 0x1C, 0x63, 0x3B, 0x81];
        $len = strlen($src);
        $res = "";
        for ($i = $j = 0; $i < $len; $i++) {
            $res .= str_pad(dechex(ord($src[$i]) ^ $keys[$j]), 2, "0", STR_PAD_LEFT);
            $j   = ++$j % 8;
        }
        return $res;
    }

    /**
     * 校验位
     *校验位计算方式如下
     *取出该数的奇数位的和
     *取出该数的偶数位的和
     *将奇数位的和与“偶数位的和的三倍”相加
     *取出结果的个位数
     *用10减去这个个位数
     *对得到的数再取个位数
     */
    public static function createNumber($rand)
    {
        $randArr = str_split($rand);
        $oddNumberArr = [];
        $evenNumber = [];
        foreach($randArr as $k => $v){
            $num = $k + 1;
            if($num % 2 == 0){
                $evenNumber[] = $v;
            } else {
                $oddNumberArr[] = $v;
            }
        }

        $number = array_sum($oddNumberArr) + array_sum($evenNumber) * 3;

        $number = substr($number,'-1');

        $number = 10 - intval($number);

        $number = substr($number,'-1');

        return $number;
    }

    //查询条件去重
    //筛选数据
    public static function SearchIntersect($intersectArr)
    {
        if(empty($intersectArr[0])){
            return 0;
        }
        $count = count($intersectArr);

        if ($count > 1) {
            $intersect = $intersectArr[0];

            for ($i = 1; $i < $count; $i++) {
                $intersect = array_intersect($intersect, $intersectArr[$i]);
            }

            $idArr = implode(',', $intersect);

        } else {

            $idArr = implode(',', $intersectArr[0]);

        }

        return $idArr;
    }

    //判断是否是json
    public static function isJson($json_str)
    {
        try {
            if(is_array(json_decode($json_str,true))){
                return true;
            }
        } catch(\Exception $e){
            return false;
        }

        return false;
    }



    // 字符串裁剪
    public static function cropContent($content, $cropLength){

        $len=$cropLength * 2;

        $str = mb_strimwidth($content, 0, $len, '...', 'utf8');
        return  $str;
    }

}
