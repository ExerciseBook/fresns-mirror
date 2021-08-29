<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Http\Center\Common\GlobalService;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\FresnsDb\FresnsCodeMessages\FresnsCodeMessagesService;

trait PluginTrait
{
    // 插件错误码
    public $code;

    // 插件错误码信息
    public $msg;

    // 插件返回的data
    public $data = [];

    // 错误码映射
    public $codeMap = [];

    /**
     * 插件初始化.
     * @return bool
     */
    public function initPlugin()
    {
        return true;
    }

    /**
     * 调用成功
     */
    protected function pluginSuccess($data = [])
    {
        $code = BasePluginConfig::OK;
        $msg = 'ok';

        // $pluginConfig = $m->pluginConfig; // 获取 unikey 方法 1
        $uniKey = $this->pluginConfig->uniKey; // 获取 unikey 方法 2
        $langTag = GlobalService::getGlobalKey('langTag');
        $message = FresnsCodeMessagesService::getCodeMessage($uniKey, $langTag, $code);
        if (empty($message)) {
            $message = $msg;
        }

        return $this->output($code, $message, $data);
    }

    /**
     * 调用异常.
     */
    protected function pluginError($code, $data = [], $msg = '')
    {
        $c = get_called_class();
        $m = new $c;
        $codeMap = $m->getPluginCodeMap();
        // $pluginConfig = $m->pluginConfig; // 获取 unikey 方法 1
        $uniKey = $this->pluginConfig->uniKey; // 获取 unikey 方法 2
        $langTag = GlobalService::getGlobalKey('langTag');
        $message = FresnsCodeMessagesService::getCodeMessage($uniKey, $langTag, $code);
        if (empty($message)) {
            $message = ErrorCodeService::getMsg($code, $data) ?? "插件检查异常[{$code}]";
        }

        return $this->output($code, $message, $data);
    }

    /**
     * 插件返回数据.
     */
    protected function output($code, $msg, $data)
    {
        $ret = [];
        $ret['plugin_code'] = $code;
        $ret['plugin_msg'] = $msg;
        $ret['plugin_data']['output'] = $data;

        return $ret;
    }

    // 错误码映射
    public function getPluginCodeMap()
    {
        return $this->codeMap;
    }
}
