<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Helper;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Http\Center\Base\BaseInstaller;
use App\Http\Center\Base\BasePlugin;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Center\Base\PluginConst;
use App\Http\Center\Common\LogService;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class PluginHelper
{

    // 获取插件类
    public static function findPluginClass($uniKey)
    {
        $pluginClass = "\\App\\Plugins\\{$uniKey}\\Plugin";
        LogService::info('获取插件类', $pluginClass);
        if (! class_exists($pluginClass)) {
            LogService::error('插件类不存在', $pluginClass);

            return null;
        }

        return new $pluginClass();
    }

    // 获取插件类
    public static function findPluginConfigClass($uniKey): ?BasePluginConfig
    {
        $configClass = "\\App\\Plugins\\{$uniKey}\\PluginConfig";
        if (! class_exists($configClass)) {
            LogService::error('配置类不存在', $configClass);

            return null;
        }

        return new $configClass();
    }

    // 获取生成插件安装类
    public static function findInstaller($uniKey): ?BaseInstaller
    {
        $installClass = "\\App\\Plugins\\{$uniKey}\\Installer";
        if (! class_exists($installClass)) {
            LogService::error('安装类不存在', $installClass);

            return null;
        }

        return new $installClass();
    }

    // 框架语言路径
    public static function frameworkLangPath($uniKey)
    {
        $langRoot = self::langRoot();

        return $langRoot;
    }

    // 插件 assets 目录
    public static function extensionAssetsPath($uniKey)
    {
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'assets']);

        return $path;
    }

    // 插件 views 目录
    public static function extensionViewPath($uniKey)
    {
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'views']);

        return $path;
    }

    // 插件 lang 目录
    public static function extensionLangPath($uniKey)
    {
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'lang']);

        return $path;
    }

    // 插件 Routes 目录
    public static function extensionRoutePath($uniKey)
    {
        $extensionRootPath = InstallHelper::getPluginExtensionPath($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$extensionRootPath, 'Routes']);

        return $path;
    }

    // 框架 views 目录
    public static function frameworkViewPath($uniKey)
    {
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::viewRoot(), $uniKey]);

        return $path;
    }

    // 框架 assets 目录
    public static function frameworkAssetsPath($uniKey)
    {
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::assetsRoot(), $uniKey]);

        return $path;
    }

    // 框架 theme 目录
    public static function frameworkThemePath($uniKey)
    {
        $path = implode(DIRECTORY_SEPARATOR, [PluginHelper::themeRoot(), $uniKey]);

        return $path;
    }

    // 插件语言路径
    public static function pluginLangPath($uniKey)
    {
        $currPluginRoot = self::currPluginRoot($uniKey);
        $path = implode(DIRECTORY_SEPARATOR, [$currPluginRoot, 'Resources', 'lang']);

        return $path;
    }

    // 根据 unikey 删除文件
    public static function uninstallByUniKey($uniKey)
    {
        // 删除模版文件
        InstallHelper::deletePluginFiles($uniKey);

        // 插件目录
        $pluginPath = PluginHelper::currPluginRoot($uniKey);
        if (is_dir($pluginPath)) {
            File::deleteDirectory($pluginPath);
        }

        $info = [];
        $info['pluginDir'] = $pluginPath;

        return $info;
    }

    /**
     * 获取某个插件目录.
     */
    public static function currPluginRoot($uniKey)
    {
        $pathArr = [base_path(), 'app', 'Plugins', $uniKey];
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        return $path;
    }

    // 插件运行根目录
    public static function pluginRoot()
    {
        $pathArr = [base_path(), 'app', 'Plugins'];

        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件模版目录
    public static function themeRoot()
    {
        $pathArr = [base_path(), 'resources', 'views', 'themes'];

        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件配置视图目录
    public static function viewRoot()
    {
        $pathArr = [base_path(), 'resources', 'views', 'plugins'];

        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件配置视图目录
    public static function langRoot()
    {
        $pathArr = [base_path(), 'resources', 'lang'];

        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 插件配置静态文件目录
    public static function assetsRoot()
    {
        $pathArr = [base_path(), 'public', 'assets'];

        return implode(DIRECTORY_SEPARATOR, $pathArr);
    }

    // 下载路径
    public static function getDownloadPath()
    {
        $pathArr = [
            base_path(),
            'public',
            'storage',
            'extensions',
        ];
        $downloadPath = implode(DIRECTORY_SEPARATOR, $pathArr);
        FileHelper::assetDir($downloadPath);

        return $downloadPath;
    }

    // 插件是否安装、启用
    public static function pluginCanUse($uniKey)
    {
        // 获取安装类
        $installer = InstallHelper::findInstaller($uniKey);
        if (empty($installer)) {
            LogService::info('info', '未找到插件类');

            return false;
        }
        $plugin = FresnsPlugins::where('unikey', $uniKey)->where('is_enable', 1)->first();
        if (empty($plugin)) {
            LogService::info('info', '插件未启用');

            return false;
        }

        return true;
    }

    public static function getPluginImageUrl(BasePluginConfig $pluginConfig)
    {
        $type = $pluginConfig->type;
        $uniKey = $pluginConfig->uniKey;

        $imgName = PluginConst::PLUGIN_IMAGE_NAME;
        // $domain = CommonHelper::domain();
        $domain = request()->server('HTTP_ORIGIN');
        // $domain = $server['HTTP_ORIGIN'];
        LogService::info('server', request()->server());

        LogService::info('domain', $domain);

        $url = $domain."/views/{$uniKey}/{$imgName}";
        LogService::info('url', $url);

        if ($type == PluginConst::PLUGIN_TYPE_THEME) {
            $url = $domain."/themes/{$uniKey}/{$imgName}";
        }

        return $url;
    }
}
