<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Base\Controllers\BaseController;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\ValidateService;
use App\Http\Center\Helper\InstallHelper;
use App\Http\Center\Helper\PluginHelper;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Basic controller class
 * Handle basic operations such as install/uninstall/upgrade of plugins.
 */
class PluginController extends BaseController
{
    // Fresns Store Install Plugin
    public function install(Request $request)
    {
        $uniKey = $request->input('unikey');
        $dirName = $uniKey;
        $downloadUrl = $request->input('downloadUrl');

        // Get the download address according to unikey
        $downloadPath = PluginHelper::getDownloadPath();

        $pathArr = [
            $downloadPath,
            $dirName.'.zip',
        ];
        $downloadFileName = implode(DIRECTORY_SEPARATOR, $pathArr);

        $content = file_get_contents($downloadUrl);

        file_put_contents($downloadFileName, $content);

        $fileSize = File::size($downloadFileName);

        if ($fileSize < 10) {
            $this->error(ErrorCodeService::DOWMLOAD_ERROR, ['info' => '下载失败或者文件为空']);
        }

        // 1. Install files
        $options = [];
        $installFileInfo = InstallHelper::installPluginFile($uniKey, $dirName, $downloadFileName, $options);

        $info = [];
        $info['downloadFileName'] = $downloadFileName;
        $info['installFileInfo'] = $installFileInfo;

        // 2. Distribution of documents
        InstallHelper::pushPluginResourcesFiles($uniKey);

        // 3. Execute the installation function of the plugin itself
        $installer = InstallHelper::findInstaller($uniKey);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $installInfo = $installer->install();
        $info['installInfo'] = $installInfo;

        $this->success($info);
    }

    /**
     * Uninstall the plugin.
     * @param Request $request
     */
    public function uninstall(Request $request)
    {
        $uniKey = $request->input('unikey');

        $info = PluginHelper::uninstallByUniKey($uniKey);
        // Delete plugin data
        FresnsPlugins::where('unikey', $uniKey)->delete();

        $this->success($info);
    }

    /**
     * Upgrade plugin.
     * @param Request $request
     */
    public function upgrade(Request $request)
    {
        $rule = [
            'unikey' => 'required',
            'localVision' => 'required',
            'dirName' => 'required',
            'remoteVision' => 'required',
            'remoteVisionInt' => 'required',
            'downloadUrl' => 'required',
        ];
        ValidateService::validateRule($request, $rule);
        $unikey = $request->input('unikey');
        $localVision = $request->input('localVision');
        $dirName = $request->input('dirName');
        $remoteVisionInt = $request->input('remoteVisionInt');
        $remoteVision = $request->input('remoteVision');
        $downloadUrl = $request->input('downloadUrl');
        if ($localVision == $remoteVisionInt) {
            $this->errorInfo(ErrorCodeService::CODE_FAIL, ['info' => 'The current version is the same as the upgraded version']);
        }
        // Get install class
        $installer = InstallHelper::findInstaller($unikey);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }
        // Perform installation
        $res = self::beforeUpgrade($unikey, $dirName, $downloadUrl);
        if (! $res) {
            $this->error(ErrorCodeService::DOWMLOAD_ERROR, ['info' => '下载失败或者文件为空']);
        }
        $info = $installer->upgrade();
        // Update to the latest version
        FresnsPlugins::where('unikey', $unikey)->update(['version_int' => $remoteVisionInt, 'version' => $remoteVision]);

        $this->success($info);
    }

    // Perform installation before upgrading the plugin
    public static function beforeUpgrade($unikey, $dirName, $dowmloadUrl)
    {
        $unikey = $unikey;
        $dirName = $dirName;
        $downloadUrl = $dowmloadUrl;

        // Get the download address according to unikey
        $pathArr = [
            base_path(),
            'public',
            'storage',
            'export',
            $dirName.'.zip',
        ];
        $downloadFileName = implode(DIRECTORY_SEPARATOR, $pathArr);

        $content = file_get_contents($downloadUrl);

        file_put_contents($downloadFileName, $content);

        $fileSize = File::size($downloadFileName);

        if ($fileSize < 10) {
            return false;
        }

        // 1. Install files
        $options = [];
        $installFileInfo = InstallHelper::installPluginFile($unikey, $dirName, $downloadFileName, $options);

        $info = [];
        $info['downloadFileName'] = $downloadFileName;
        $info['installFileInfo'] = $installFileInfo;

        // 2. Execute the installation function of the plugin itself
        $installer = InstallHelper::findInstaller($unikey);
        if (empty($installer)) {
            return false;
            // $this->error(FresnsCode::NO_RECORD);
        }

        $installInfo = $installer->install();
        $info['installInfo'] = $installInfo;

        return $info;
    }
}
