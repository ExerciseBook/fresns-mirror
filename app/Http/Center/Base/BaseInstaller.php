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
 * 安装基础类
 */
class BaseInstaller
{
    /**
     * 安装配置类
     * @var
     */
    protected $pluginConfig;

    /**
     * 安装插件时，每个版本对应的安装函数
     * 键: 整型版本号, versionInt
     * 值: 安装函数的名称, functionName, 例如 installV1
     * @var array
     */
    public $versionIntInstallFunctionNameMap = [
        1   =>  "installV1",
    ];

    public function getPluginConfig() : BasePluginConfig{
        return $this->pluginConfig;
    }

    /**
     * 每个整型版本和三位版本的对应关系, 存储发布记录
     * 键: 整型版本号, versionInt
     * 值: 三位版本号 , version, 例如 1.0.0
     * @var array
     */
    public $versionIntToVersionMap = [
        1   =>  '1.0.0',
    ];

    /**
     * 安装
     */
    public function install(){


    }

    /**
     * 打包
     */
    public function package(){
        $config = $this->getPluginConfig();
        $uniKey = $config->uniKey;

        // 复制文件
        InstallHelper::pullPluginResourcesFiles($uniKey);


        // 插件目录
        $pluginDir = $config->dirName;
        $versionInt = $config->currVersionInt;
        $options = [];
        $info = InstallHelper::packagePluginFile($uniKey, $pluginDir, $versionInt, $options);


        return $info;
    }

    /**
     * 卸载
     * 删除插件的目录
     */
    public function uninstall(){
        $config = $this->getPluginConfig();
        $uniKey = $config->uniKey;

        // 删除模版文件
        InstallHelper::deletePluginFiles($uniKey);

        // 插件目录
        $pluginPath = PluginHelper::currPluginRoot($uniKey);
        if(is_dir($pluginPath)){
            File::deleteDirectory($pluginPath);
        }

        $info = [];
        $info['pluginDir'] = $pluginPath;

        return $info;
    }

    /**
     * 升级
     */
    public function upgrade(){

        //  如果当前版本有安装函数，则执行安装函数
        // $currVersionInt = $this->pluginConfig->currVersionInt;
        $currVersionInt = request()->input('localVision');
        $remoteVision = request()->input('remoteVision');
        $installFunc = $this->versionIntInstallFunctionNameMap;
        // dd($installFunc);
        for ($i = $currVersionInt + 1; $i  <= $remoteVision ; $i ++) {
            $installFunc = $this->versionIntInstallFunctionNameMap[$i] ?? '';
            if(!empty($installFunc)){
                $this->$installFunc();
            }
        }
        return true;
    }

    /**
     * 生成插件信息的json文件
     */
    public function generateJsonFile(){
        $pluginInfo = [];
        $config = $this->getPluginConfig();

        $pluginInfo['uniKey'] = $config->uniKey;
        $pluginInfo['name'] = $config->name;
        $pluginInfo['description'] = $config->description;
        $pluginInfo['author'] = $config->author;
        $pluginInfo['authorLink'] = $config->authorLink;
        $pluginInfo['dirName'] = $config->dirName;
        $pluginInfo['currVersion'] = $config->currVersion;
        $pluginInfo['currVersionInt'] = $config->currVersionInt;
        $pluginInfo['scene'] = $config->sceneArr;
        $pluginInfo['theme']['PC'] = $config->getThemePc();
        $pluginInfo['theme']['Mobile'] = $config->getThemeMobile();

        $pathInfoArr = [
            base_path(),
            'app',
            'Plugins',
            $config->uniKey,
            'plugin.json',
        ];

        $fileName = implode(DIRECTORY_SEPARATOR, $pathInfoArr);

        $jsonContent = json_encode($pluginInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($fileName, $jsonContent);

        $info = [];
        $info['filename'] = $fileName;
        $info['pluginInfo'] = $pluginInfo;

        return $info;
    }
}
