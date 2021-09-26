<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

trait CodeTrait
{
    //
    public static function checkInfo($code)
    {
        $c = get_called_class();
        dd($c);
        $m = new $c;
        $codeMap = $m->getCodeMap();
        dd($codeMap);
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
}
