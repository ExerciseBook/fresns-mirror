<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Http\Center\Helper\InstallHelper;
use App\Http\Center\Helper\PluginHelper;
use Illuminate\Support\Facades\File;

/**
 * 安装基础类.
 */
class BaseInstaller
{
    /**
     * 安装配置类.
     */
    protected $pluginConfig;

    /**
     * 安装插件时，每个版本对应的安装函数
     * 键: 整型版本号, versionInt
     * 值: 安装函数的名称, functionName, 例如 installV1.
     * @var array
     */
    public $versionIntInstallFunctionNameMap = [
        1   =>  'installV1',
    ];

    public function getPluginConfig(): BasePluginConfig
    {
        return $this->pluginConfig;
    }

    /**
     * 每个整型版本和三位版本的对应关系, 存储发布记录
     * 键: 整型版本号, versionInt
     * 值: 三位版本号 , version, 例如 1.0.0.
     * @var array
     */
    public $versionIntToVersionMap = [
        1   =>  '1.0.0',
    ];

    /**
     * 安装时候执行的额外操作，示例：往数据库写入插件配置文件
     *
     */
    public function install()
    {
        //
    }

    /**
     * 卸载时候执行的额外操作，示例：删除数据库中的插件配置文件
     */
    public function uninstall()
    {
        //
    }

    /**
     * 升级.
     */
    public function upgrade()
    {
        // 如果当前版本有安装函数，则执行安装函数
        // $currVersionInt = $this->pluginConfig->currVersionInt;
        $currVersionInt = request()->input('localVision');
        $remoteVision = request()->input('remoteVision');
        $installFunc = $this->versionIntInstallFunctionNameMap;
        // dd($installFunc);
        for ($i = $currVersionInt + 1; $i <= $remoteVision; $i++) {
            $installFunc = $this->versionIntInstallFunctionNameMap[$i] ?? '';
            if (! empty($installFunc)) {
                $this->$installFunc();
            }
        }

        return true;
    }
}
