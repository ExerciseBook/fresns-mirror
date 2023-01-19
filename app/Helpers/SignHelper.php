<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

class SignHelper
{
    const SIGN_PARAM_ARR = [
        'X-Fresns-App-Id',
        'X-Fresns-Client-Platform-Id',
        'X-Fresns-Client-Version',
        'X-Fresns-Aid',
        'X-Fresns-Aid-Token',
        'X-Fresns-Uid',
        'X-Fresns-Uid-Token',
        'X-Fresns-Signature-Timestamp',
    ];

    // Check Sign
    public static function checkSign(array $signMap, string $appSecret): bool
    {
        $inputSign = $signMap['X-Fresns-Signature'];
        unset($signMap['X-Fresns-Signature']);

        $makeSign = SignHelper::makeSign($signMap, $appSecret);

        return $inputSign == $makeSign;
    }

    // Make Sign
    public static function makeSign(array $signMap, string $appSecret): string
    {
        $signParams = collect($signMap)->filter(function ($value, $key) {
            return in_array($key, SignHelper::SIGN_PARAM_ARR);
        })->toArray();

        $signParams = array_filter($signParams);

        ksort($signParams);

        $params = http_build_query($signParams);

        $signData = $params."&AppSecret={$appSecret}";

        // Generate sign
        $sign = md5($signData);

        return $sign;
    }
}
