<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Http\Share\Common\LogService;

class SignHelper
{

    public static function checkSign($dataMap, $signKey){

        $inputSign = $dataMap['sign'];
        unset($dataMap['sign']);

        $genSign = self::genSign($dataMap, $signKey);
        $info = [];
        $info['input_sign'] = $inputSign;
        $info['gen_sign'] = $genSign;
        LogService::info("check sign: ", $info);

        if ($inputSign == $genSign) {
            return true;
        }

        return $info;
    }

    public static function genSign($dataMap , $signKey){
        // 对数组的值按key排序
        ksort($dataMap);
        // 生成url的形式
        $params = http_build_query($dataMap);
        $params = $params . "&key={$signKey}";
        // 生成sign
        $sign = md5($params);
        return $sign;
    }

}
