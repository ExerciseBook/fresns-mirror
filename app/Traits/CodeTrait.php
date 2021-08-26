<?php
/*
 * Fresns
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
        $m = new $c;
        $codeMap = $m->getCodeMap();
        $data = [
            'code'  => $code,
            'msg'   => $codeMap[$code] ?? '业务检查异常',
        ];

        return $data;
    }

    public function getCodeMap()
    {
        return $this->codeMap;
    }
}
