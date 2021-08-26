<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Base\Controllers\BaseController;
use App\Http\Center\Helper\InstallHelper;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Fresns\FresnsPlugins\FresnsPlugins;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\ValidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * 基础控制器类
 * 处理插件的安装/卸载/升级等基础操作.
 */
class PluginController extends BaseController
{
    // 远程安装插件
    public function install(Request $request)
    {
        $uniKey = $request->input('unikey');
        $dirName = $uniKey;
        $downloadUrl = $request->input('downloadUrl');

        // 根据unikey获取下载地址
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

        // 1. 安装文件
        $options = [];
        $installFileInfo = InstallHelper::installPluginFile($uniKey, $dirName, $downloadFileName, $options);

        $info = [];
        $info['downloadFileName'] = $downloadFileName;
        $info['installFileInfo'] = $installFileInfo;

        // 2. 分发文件
        InstallHelper::pushPluginResourcesFiles($uniKey);

        // 3. 执行插件本身的安装函数
        $installer = InstallHelper::findInstaller($uniKey);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $installInfo = $installer->install();
        $info['installInfo'] = $installInfo;

        $this->success($info);
    }

    /**
     * 打包插件.
     * @param Request $request
     */
    public function package(Request $request)
    {
        $unikey = $request->input('unikey');

        // 获取安装类
        $installer = InstallHelper::findInstaller($unikey);
        // dd($installer);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $info = $installer->package();

        $this->success($info);
    }

    /**
     * 卸载插件.
     * @param Request $request
     */
    public function uninstall(Request $request)
    {
        $uniKey = $request->input('unikey');

//        // 获取安装类
//        $installer = InstallHelper::findInstaller($uniKey);
//        if(empty($installer)){
//            $this->error(ErrorCodeService::NO_RECORD);
//        }
//        $info = $installer->uninstall();

        $info = PluginHelper::uninstallByUniKey($uniKey);
        // 删除插件数据
        FresnsPlugins::where('unikey', $uniKey)->delete();

        $this->success($info);
    }

    /**
     * 升级插件.
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
            $this->errorInfo(ErrorCodeService::CODE_FAIL, ['info' => '当前版本与升级版本一致']);
        }
        // 获取安装类
        $installer = InstallHelper::findInstaller($unikey);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }
        // 执行安装
        $res = self::beforeUpgrade($unikey, $dirName, $downloadUrl);
        if (! $res) {
            $this->error(ErrorCodeService::DOWMLOAD_ERROR, ['info' => '下载失败或者文件为空']);
        }
        $info = $installer->upgrade();
        // 更新至最新版本
        FresnsPlugins::where('unikey', $unikey)->update(['version_int' => $remoteVisionInt, 'version' => $remoteVision]);

        $this->success($info);
    }

    /**
     * @param Request $request
     */
    public function genDescJson(Request $request)
    {
        $unikey = $request->input('unikey');

        // 获取安装类
        $installer = InstallHelper::findInstaller($unikey);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $info = $installer->generateJsonFile();

        $this->success($info);
    }

    // 升级插件之前执行安装
    public static function beforeUpgrade($unikey, $dirName, $dowmloadUrl)
    {
        $unikey = $unikey;
        $dirName = $dirName;
        $downloadUrl = $dowmloadUrl;

        // 根据unikey获取下载地址
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

        // 1. 安装文件
        $options = [];
        $installFileInfo = InstallHelper::installPluginFile($unikey, $dirName, $downloadFileName, $options);

        $info = [];
        $info['downloadFileName'] = $downloadFileName;
        $info['installFileInfo'] = $installFileInfo;

        // 2. 执行插件本身的安装函数
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
