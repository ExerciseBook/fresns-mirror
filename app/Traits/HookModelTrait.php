<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

// 模版设置
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Route;

trait HookModelTrait
{
    public $model;

    public function setModel($m)
    {
        $this->model = $m;
    }

    // 钩子函数: 初始化数据库
    public function hookConnectionInit()
    {
        return true;
    }

    // 钩子函数: 模型初始化
    public function hookModelInit()
    {
        return true;
    }

    // 钩子函数: 模型附加表条件初始化
    // 场景： 附表搜索条件
    public function hookModelInitAppend()
    {
        return true;
    }

    // 钩子函数: 模型创建完成之后
    public function hookStoreAfter($id)
    {
        return $id;
    }

    // 钩子函数: 批量删除之前
    public function hookDestroyBefore($idArr)
    {
        foreach ($idArr as $id) {
            $this->hookDestroyItemBefore($id);
        }

        return $idArr;
    }

    // 钩子函数: 单个删除之前
    public function hookDestroyItemBefore($id)
    {
        return $id;
    }
}
