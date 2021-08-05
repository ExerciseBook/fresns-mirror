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

trait HookControllerTrait
{

    // 钩子函数: store 验证之后, 如二次验证
    public function hookStoreValidateAfter(){
        return true;
    }

    // 钩子函数: update 验证之后, 如二次验证
    public function hookUpdateValidateAfter(){
        return true;
    }


    // 检查服务器返回
    public function checkServerResp($serverResp){

        return true;
    }

    // 格式化服务器返回
    public function formatServerResp($serverResp){
        $code = $serverResp['server_code'];
        $msg = $serverResp['server_msg'];
        $output = [];
        $common = [];

        $serverData = $serverResp['server_data'];

        if(isset($serverData['output'])){
            $output = $serverData['output'];
        }
        if(isset($serverData['common'])){
            $common = $serverData['common'];
        }

        $ret = [];
        $ret['code'] = $code;
        $ret['msg'] = $msg;
        $ret['output'] = $output;
        $ret['common'] = $common;
        $ret['data']['output'] = $output;
        $ret['data']['common'] = $common;

        return $ret;
    }
}