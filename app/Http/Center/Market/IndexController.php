<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Market;

use App\Base\Controllers\BaseAdminController;
use App\Http\Center\Helper\PluginHelper;
use Illuminate\Http\Request;

// 后台插件
class IndexController extends BaseAdminController
{

    /**
     * 获取本地插件
     * @param Request $request
     */
    public function index(Request  $request){

        // 扫描 addons 下的 json文件，获取到插件信息，展示在插件列表

        $data = PluginHelper::getPluginJsonFileArr();


        $this->success($data);
    }



}
