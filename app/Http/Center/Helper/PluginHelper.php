<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Helper;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Share\Common\LogService;
use App\Http\Center\Base\BaseInstaller;
use App\Http\Center\Base\BasePlugin;
use App\Http\Center\Base\PluginConst;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use App\Http\Fresns\FresnsPlugins\FresnsPlugins;
class PluginHelper
{
    // 获取插件json文件
    public static function getPluginJsonFileArr(){
        $scanDir = self::pluginRoot();

        $pluginJsonArr = [];
        $pluginUniKeyArr = [];
        $dir = new \DirectoryIterator( $scanDir);
        // dd($dir);
        foreach ($dir as $file){
            // 遍历子目录
            if($file->isDir()){
                $subDir = new \DirectoryIterator($file->getPathname());
                foreach ($subDir as $subFile) {
                    $pluginJsonFile = implode(DIRECTORY_SEPARATOR, [$subFile->getPath(), PluginConst::PLUGIN_JSON_FILE_NAME]);
                    if(file_exists($pluginJsonFile)){
                        $pluginJson = json_decode(file_get_contents($pluginJsonFile), true);
                        $uniKey = $pluginJson['uniKey'] ?? '';
                        if(!in_array($uniKey, $pluginUniKeyArr)){
                            $pluginUniKeyArr[] = $uniKey;
                            $pluginJsonArr[] = $pluginJson;
                        }
                    }
                }
            }
        }

        return $pluginJsonArr;
    }


    // 获取插件类
    public static function findPluginClass($uniKey) {
        $pluginClass = "\\App\\Plugins\\{$uniKey}\\Plugin";
        LogService::info("获取插件类",$pluginClass);
        if(!class_exists($pluginClass)){
            LogService::error("插件类不存在", $pluginClass);
            return NULL;
        }
        return new $pluginClass();
    }

    // 获取插件类
    public static function findPluginConfigClass($uniKey) : ?BasePluginConfig  {
        $configClass = "\\App\\Plugins\\{$uniKey}\\PluginConfig";;
        if(!class_exists($configClass)){
            LogService::error("配置类不存在", $configClass);
            return NULL;
        }
        return new $configClass();
    }

    // 获取生成插件安装类
    public static function findInstaller($uniKey): ?BaseInstaller{
        $installClass =   "\\App\\Plugins\\{$uniKey}\\Installer";
        if(!class_exists($installClass)){
            LogService::error("安装类不存在", $installClass);
            return NULL;
        }
        return new $installClass();
    }

    // 框架PC端模版
    public static function frameworkThemePcPath($uniKey){
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $themePc = $pluginConfig->getThemePc();
        $themeRoot = PluginHelper::themeRoot();

        $path = implode(DIRECTORY_SEPARATOR, [$themeRoot, $themePc]);
        return $path;
    }

    // 框架移动端模版
    public static function frameworkThemeMobilePath($uniKey){
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $themeMobile = $pluginConfig->getThemeMobile();
        $themeRoot = PluginHelper::themeRoot();

        $path = implode(DIRECTORY_SEPARATOR, [$themeRoot, $themeMobile]);
        return $path;
    }

    // 框架设置页面路径
    public static function frameworkViewSettingPath($uniKey){
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $viewSetting = $pluginConfig->getViewSetting();

        $viewRoot = self::viewRoot();
        $path = implode(DIRECTORY_SEPARATOR, [$viewRoot, $viewSetting]);
        return $path;
    }

    // 框架语言路径
    public static function frameworkLangPath($uniKey){
        $langRoot = self::langRoot();
        return $langRoot;
    }

    // 插件PC端模版
    public static function pluginThemePcPath($uniKey){
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $themePc = $pluginConfig->getThemePc();

        $currPluginRoot = self::currPluginRoot($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$currPluginRoot, 'Resources', $themePc]);
        return $path;
    }


    // 插件 views 目录
    public static function extensionViewPath($uniKey){
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'views']);
        return $path;
    }

    // 插件 views 目录
    public static function extensionLangPath($uniKey){
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'lang']);
        return $path;
    }

    // 插件 views 目录
    public static function extensionRoutePath($uniKey){
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'Route']);
        return $path;
    }

    // 插件 image 目录
    public static function extensionImagePath($uniKey){
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, $uniKey . ".png"]);
        return $path;
    }

    // 插件 设置文件 目录
    public static function extensionSettingsPath($uniKey)
    {
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'settings.php']);
        return $path;
    }

    // 插件 设置文件 目录
    public static function frameworkSettingsPath($uniKey)
    {
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::themeRoot(), $uniKey, 'settings.php']);
        return $path;
    }

    // 框架 views 目录
    public static function frameworkViewPath($uniKey){
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::viewRoot(), $uniKey]);
        return $path;
    }

    // 框架 theme 目录
    public static function frameworkThemePath($uniKey){
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::themeRoot(), $uniKey]);
        return $path;
    }

    // 框架 theme image 目录
    public static function frameworkThemeImagePath($uniKey){
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::themeRoot(), $uniKey, $uniKey . ".png"]);
        return $path;
    }

    // 框架 views image 目录
    public static function frameworkImagePath($uniKey){
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::viewRoot(), $uniKey, $uniKey . ".png"]);
        return $path;
    }

    // 插件移动端模版
    public static function pluginThemeMobilePath($uniKey){
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $themeMobile = $pluginConfig->getThemeMobile();

        $currPluginRoot = self::currPluginRoot($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$currPluginRoot, 'Resources', $themeMobile]);
        return $path;
    }

    // 插件设置页面路径
    public static function pluginViewSettingPath($uniKey){
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $viewSetting = $pluginConfig->getViewSetting();
        $currPluginRoot = self::currPluginRoot($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$currPluginRoot, 'Resources', $viewSetting]);
        return $path;
    }

    // 插件语言路径
    public static function pluginLangPath($uniKey){
        $currPluginRoot = self::currPluginRoot($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$currPluginRoot, 'Resources', 'lang']);
        return $path;
    }

    // 根据 unikey 删除文件
    public static function uninstallByUniKey($uniKey){
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
     * 获取某个插件目录
     */
    public static function currPluginRoot($uniKey){
        $pathArr = [ base_path(), 'app', 'Plugins', $uniKey];
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);
        return $path;
    }

    // 插件运行根目录
    public static function pluginRoot(){
        $pathArr = [ base_path(), 'app', 'Plugins' ];
        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件模版目录
    public static function themeRoot(){
        $pathArr = [ base_path(), 'public', 'themes' ];
        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件配置视图目录
    public static function viewRoot(){
        $pathArr = [ base_path(), 'public', 'views' ];
        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件配置视图目录
    public static function langRoot(){
        $pathArr = [ base_path(), 'resources', 'lang' ];
        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 下载路径
    public static function getDownloadPath(){
        $pathArr = [
            base_path(),
            'public',
            'storage',
            'download',
        ];
        $downloadPath = implode(DIRECTORY_SEPARATOR, $pathArr);
        FileHelper::assetDir($downloadPath);

        return $downloadPath;
    }

    // 插件是否安装、启用
    public static function pluginCanUse($uniKey){
        // 获取安装类
        $installer = InstallHelper::findInstaller($uniKey);
        if(empty($installer)){
            LogService::info('info',"未找到插件类");
            return false;
        }
        $plugin = FresnsPlugins::where('unikey',$uniKey)->where('is_enable',1)->first();
        if(empty($plugin)){
            LogService::info('info',"插件未启用");
            return false;
        }
        return true;
    }

    // 获取插件json文件
    public static function getPluginJsonFileArrByDirName($dirName){
        // $scanDir = self::pluginRoot();
        $pathArr = [ base_path(), 'public', 'storage','plugins',$dirName];
        $scanDir =  implode(DIRECTORY_SEPARATOR, $pathArr);
        $pluginJsonArr = [];
        $pluginUniKeyArr = [];
        $dir = new \DirectoryIterator( $scanDir);
        // dd($dir);
        foreach ($dir as $file){
            // 遍历子目录
            if($file->isDir()){
                $subDir = new \DirectoryIterator($file->getPathname());
                foreach ($subDir as $subFile) {
                    $pluginJsonFile = implode(DIRECTORY_SEPARATOR, [$subFile->getPath(), PluginConst::PLUGIN_JSON_FILE_NAME]);
                    if(file_exists($pluginJsonFile)){
                        $pluginJson = json_decode(file_get_contents($pluginJsonFile), true);
                        $uniKey = $pluginJson['uniKey'] ?? '';
                        if(!in_array($uniKey, $pluginUniKeyArr)){
                            $pluginUniKeyArr[] = $uniKey;
                            $pluginJsonArr = $pluginJson;
                        }
                    }
                }
            }
        }

        return $pluginJsonArr;
    }

    public static function getPluginImageUrl(BasePluginConfig $pluginConfig){
        $type = $pluginConfig->type;
        $uniKey = $pluginConfig->uniKey;

        $imgName = PluginConst::PLUGIN_IMAGE_NAME;
        // $domain = CommonHelper::domain();
        $domain= request()->server('HTTP_ORIGIN');
        // $domain = $server['HTTP_ORIGIN'];
        LogService::info('server',request()->server());

        LogService::info('domain',$domain);

        $url = $domain . "/views/{$uniKey}/{$imgName}";
        LogService::info('url',$url);

        if($type == PluginConst::PLUGIN_TYPE_THEME){
            $url = $domain . "/themes/{$uniKey}/{$imgName}";
        }

        return $url;
    }
}
