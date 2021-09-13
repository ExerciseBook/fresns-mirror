<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Route;

trait HookServiceTrait
{
    // Hook functions: service (initializing)
    public function hookInit()
    {
        return true;
    }

    // Hook functions: tree service (Before the list, initialize the query criteria)
    public function hookListTreeBefore()
    {
        return true;
    }
}
