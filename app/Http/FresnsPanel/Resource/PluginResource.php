<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsPanel\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\Helper\PluginHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsCmd\FresnsPlugin;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins as TweetPlugin;
use App\Http\FresnsDb\FresnsPlugins\FresnsPluginsConfig;

class PluginResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // form 字段
        $formMap = FresnsPluginsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // 插件是否下载
        // $doloadPlugin = PluginHelper::getPluginJsonFileArr();
        $pluginConfig = PluginHelper::findPluginConfigClass($this->unikey);
        // dump($pluginConfig);
        $isDownload = FresnsPluginsConfig::NO_DOWNLOAD;
        if ($pluginConfig) {
            if ($pluginConfig->uniKey == $this->unikey) {
                $isDownload = FresnsPluginsConfig::DOWNLOAD;
            }
        }
        // 插件目录下的json文件
        // dd($pluginConfig);
        // 是否下载
        // $isDownload = TweetPluginConfig::NO_DOWNLOAD;
        // $isNewVision = TweetPluginConfig::NO_NEWVISION;
        // // dd($localPlugin);
        // if($doloadPlugin){
        //     foreach($doloadPlugin as $d){
        //         if($this->unikey == $d['uniKey']){
        //             $isDownload = TweetPluginConfig::DOWNLOAD;
        //         }
        //     }

        // }
        // 是否有新版本
        $isNewVision = FresnsPluginsConfig::NO_NEWVISION;
        // 获取远程的插件版本
        // $localPlugin = PluginHelper::getPluginJsonFileArrByDirName($this->unikey);
        // // dd($localPlugin);
        // $newVisionInt = "";
        // $newVision = "";
        // if($localPlugin){
        //         if($this->unikey == $localPlugin['uniKey']){
        //             if($this->version_int != $localPlugin['currVersionInt']){
        //                 $isNewVision = TweetPluginConfig::NEWVISION;
        //                 $newVisionInt = $localPlugin['currVersionInt'];
        //                 $newVision = $localPlugin['currVersion'];
        //             }
        //     }
        // }
        $websitePc = '';
        $websiteMobile = '';
        $websitePcPlugin = '';
        $websiteMobilePlugin = '';
        // 网站引擎关联模板
        if ($this->type == 1) {
            $websitePc = ApiConfigHelper::getConfigByItemKey($this->unikey.'_Pc');
            $websitePcPlugin = TweetPlugin::where('unikey', $websitePc)->first();
            $websitePcPlugin = $websitePcPlugin['name'] ?? '';
            // dd($websitePc);
            $websiteMobile = ApiConfigHelper::getConfigByItemKey($this->unikey.'_Mobile');
            $websiteMobilePlugin = TweetPlugin::where('unikey', $websiteMobile)->first();
            $websiteMobilePlugin = $websiteMobilePlugin['name'] ?? '';
        }
        // dd($author);
        // 默认字段
        $default = [
            'key' => $this->id,
            'id' => $this->id,
            'is_enable' => boolval($this->is_enable),
            'disabled' => false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'more_json' => $this->more_json,
            'more_json_decode' => json_decode($this->more_json, true),
            'isDownload' => $isDownload,
            'isNewVision' => $isNewVision,
            // 'newVisionInt' => $newVisionInt,
            // 'newVision' => $newVision,
            'websitePc' => $websitePc,
            'websiteMobile' => $websiteMobile,
            'websitePcPlugin' => $websitePcPlugin ?? '',
            'websiteMobilePlugin' => $websiteMobilePlugin ?? '',
            // 'downloadUrl' => $downloadUrl,
            // 'author' => $author,
        ];

        // 合并
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
