<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Http\Share\Common\LogService;
use App\Http\Share\Common\ValidateService;

class BasePlugin
{
    use PluginTrait;

    /**
     * 服务配置类
     */
    public $pluginConfig = null;

    /**
     * 命令字映射
     *
     * @var array
     */
    public $pluginCmdHandlerMap = [];

    // 构造函数
    public function __construct()
    {
        $this->pluginConfig = new BasePluginConfig();
        $this->pluginCmdHandlerMap = $this->getPluginCmdHandlerMap();
        $this->initPlugin();
    }

    // 检查命令字
    protected function checkPluginCmdExist($cmd)
    {
        $method = $this->pluginCmdHandlerMap[$cmd] ?? '';
        if (empty($method)) {
            return false;
        }

        return true;
    }

    // 执行方法调用
    public function handle($cmd, $params, $options = [])
    {
        LogService::info("插件请求 cmd [$cmd] 参数", $params);

        // 检查命令字
        if (!$this->checkPluginCmdExist($cmd)) {
            return $this->pluginError(BasePluginConfig::CODE_NOT_EXIST);
        }

        $method = $this->pluginCmdHandlerMap[$cmd] ?? '';
        $methodRule = $method."Rule";

        //   dd($methodRule);
        // 参数校验
        if (method_exists($this->pluginConfig, $methodRule)) {

            $validRes = ValidateService::validateServerRule($params, $this->pluginConfig->{$methodRule}());

            if ($validRes !== true) {
                LogService::info("插件请求cmd [$cmd] 参数异常", $validRes);
                return $this->pluginError(BasePluginConfig::CODE_PARAMS_ERROR, $validRes);
            }
        }

        // 执行方法
        $result = $this->$method($params);

        LogService::info("插件请求 cmd [$cmd] 结果", $result);

        // 代理模式直接返回服务结果不做任何处理
        return $result;
    }

    //
    public function getPluginCmdHandlerMap()
    {
        return [];
    }

    public function getCodeMap()
    {
        return BasePluginConfig::CODE_MAP;
    }

}
