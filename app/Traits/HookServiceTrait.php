<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

// 模版设置
use App\Helpers\CommonHelper;
use App\Helpers\PluginHelper;
use Illuminate\Support\Facades\Route;

trait HookServiceTrait
{
    // 钩子函数: service 初始化
    public function hookInit()
    {
        return true;
    }

    // 钩子函数: tree service 列表之前， 初始化查询条件
    public function hookListTreeBefore()
    {
        return true;
    }
}
