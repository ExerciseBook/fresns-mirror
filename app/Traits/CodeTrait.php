<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;
use App\Http\Center\Common\ErrorCodeService;
trait CodeTrait
{
    //
    public static function checkInfo1($code)
    {
        $c = get_called_class();
        $m = new $c;
        $codeMap = $m->getCodeMap();
        $data = [
            'code'  => $code,
            'msg'   => $codeMap[$code] ?? 'Function Check Anomalies',
        ];

        return $data;
    }

    public function getCodeMap()
    {
        return $this->codeMap;
    }
    public static function checkInfo($code){
        $message = ErrorCodeService::getMsg($code);
        dd($message);
    }
}
