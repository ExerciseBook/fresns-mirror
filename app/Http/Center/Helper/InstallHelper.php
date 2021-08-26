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
use App\Http\Center\Base\PluginConst;
use App\Http\Share\Common\LogService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallHelper
{
    public static function findInstaller($uniKey): ?BaseInstaller
    {
        return PluginHelper::findInstaller($uniKey);
    }

    // 安装插件
    public static function installPluginFile($uniKey, $dirName, $downloadFileName, $options = [])
    {
        $toName = self::getExtensionsRootPath();
        $unzipResult = FileHelper::unzip($downloadFileName, $toName);

        $info = [];
        $info['unzipFromName'] = $downloadFileName;
        $info['unzipToName'] = $toName;
        $info['unzipResult'] = $unzipResult;

        return $info;
    }

    // 本地安装插件, 先将文件全量 copy 到 app/Plugins 下
    public static function installLocalPluginFile($uniKey, $dirName, $downloadFileName, $options = [])
    {
        $pluginRoot = PluginHelper::pluginRoot();
        $toName = implode(DIRECTORY_SEPARATOR, [$pluginRoot, $dirName]);
        $toName1 = implode(DIRECTORY_SEPARATOR, [$pluginRoot]);
        // dd($toName);
        $files = substr(sprintf('%o', fileperms($downloadFileName)), -4);
        clearstatcache();
        // dd($files);
        LogService::info('Auth-toName1', $downloadFileName);
        LogService::info('Auth', $files);
        self::copyPluginDirectory($downloadFileName, $toName);
        $info = [];
        $info['unzipFromName'] = $downloadFileName;
        $info['unzipToName'] = $toName;

        return $info;
    }

    /**
     * 打包插件.
     * @param       $uniKey
     * @param       $dirName
     * @param       $versionInt
     * @param array $options
     */
    public static function packagePluginFile($uniKey, $dirName, $versionInt, $options = [])
    {
        $pathArr = [base_path(), 'app', 'Plugins', $dirName];

        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        $toPath = self::getPluginStorageDir();
        $zipFilename = "{$uniKey}_V{$versionInt}.zip";

        $toPathFile = implode(DIRECTORY_SEPARATOR, [$toPath, $zipFilename]);
        $zipResult = FileHelper::zip($toPathFile, $dirName, $path, $toPath);

        $domain = CommonHelper::domain();

        $info = [];
        $info['zipToPathFile'] = $toPathFile;
        $info['zipFileName'] = $zipFilename;
        $info['zipResult'] = $zipResult;
        $info['url'] = $domain.'/storage/plugins/'.$zipFilename;

        return $info;
    }

    /*
     * 获取插件存储目录
     */
    public static function getPluginStorageDir()
    {
        $pathArr = [base_path(), 'public', 'storage', 'plugins'];
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        // 不存在则创建
        $createDir = FileHelper::assetDir($path);
        if (! $createDir) {
            LogService::error('创建目录失败', $path);

            return false;
        }

        return $path;
    }

    // 获取插件相关的所有文件
    public static function pullPluginResourcesFiles($uniKey)
    {
        $info = [];

        // pc theme
        $srcPath = PluginHelper::frameworkThemePcPath($uniKey);
        $destPath = PluginHelper::pluginThemePcPath($uniKey);

        $info['theme_pc_path_framework'] = $srcPath;
        $info['theme_pc_path_plugin'] = $destPath;

        if (! empty($srcPath)) {
            FileHelper::assetDir($destPath);
            File::copyDirectory($srcPath, $destPath);
        }

        // mobile theme
        $srcPath = PluginHelper::frameworkThemeMobilePath($uniKey);
        $destPath = PluginHelper::pluginThemeMobilePath($uniKey);
        $info['theme_mobile_path_framework'] = $srcPath;
        $info['theme_mobile_path_plugin'] = $destPath;
        if (! empty($srcPath)) {
            FileHelper::assetDir($destPath);
            File::copyDirectory($srcPath, $destPath);
        }

        // view
        $srcPath = PluginHelper::frameworkViewSettingPath($uniKey);
        $destPath = PluginHelper::pluginViewSettingPath($uniKey);
        $info['view_path_framework'] = $srcPath;
        $info['view_path_plugin'] = $destPath;

        if (! empty($srcPath)) {
            FileHelper::assetDir($destPath);
            File::copyDirectory($srcPath, $destPath);
        }

        // lang
        self::pullLang($uniKey);

        //   dd($info);
    }

    // 语言文件同步
    public static function pullLang($uniKey)
    {
        $info = [];
        $srcPath = PluginHelper::frameworkLangPath($uniKey);
        $destPath = PluginHelper::pluginLangPath($uniKey);
        $info['lang_path_framework'] = $srcPath;
        $info['lang_path_plugin'] = $destPath;

        $dir = new \DirectoryIterator($srcPath);
        foreach ($dir as $file) {
            // 遍历子目录
            if ($file->isDir()) {
                $fileName = $file->getFilename();
                if (in_array($fileName, PluginConst::PLUGIN_SKIP_DIR_ARR)) {
                    continue;
                }

                $filePath = $file->getPath();

                $frameworkLangPath = implode(DIRECTORY_SEPARATOR, [$filePath, $fileName, $uniKey]);
                $pluginLangPath = implode(DIRECTORY_SEPARATOR, [$destPath, $fileName, $uniKey]);

                // 源目录存在
                if (is_dir($frameworkLangPath)) {
                    $info['lang_sub_path_framework'] = $frameworkLangPath;
                    $info['lang_sub_path_plugin'] = $pluginLangPath;
                    FileHelper::assetDir($pluginLangPath);
                    File::copyDirectory($frameworkLangPath, $pluginLangPath);
                }
            }
        }
    }

    // 公共方法
    public static function copyPluginDirectory($srcPath, $destPath)
    {
        LogService::Info('srcPath', $srcPath);
        if (! empty($srcPath) || is_dir($srcPath)) {
            LogService::Info('destPath', $destPath);
            FileHelper::assetDir($destPath);
            File::copyDirectory($srcPath, $destPath);
        }
    }

    // 公共方法
    public static function copyPluginFile($srcFile, $destPath)
    {
        if (file_exists($srcFile)) {
            File::copy($srcFile, $destPath);
        }
    }

    // 分发插件文件到框架目录
    public static function pushPluginResourcesFiles($uniKey)
    {
        $info = [];
        // 本地插件目录
        $extensionAllPath = self::getPluginExtensionPath($uniKey);
        // 插件目录
        $pluginAllPath = self::getPluginRuntimePath($uniKey);
        $frameworkAssetsPath = PluginHelper::frameworkAssetsPath($uniKey);
        $frameworkComponentsAppPath = PluginHelper::frameworkComponentsAppPath($uniKey);
        $frameworkComponentsViewPath = PluginHelper::frameworkComponentsViewPath($uniKey);

        $extensionAssetsPath = PluginHelper::extensionAssetsPath($uniKey);
        $extensionComponentsAppPath = PluginHelper::extensionComponentsAppPath($uniKey);
        $extensionComponentsViewPath = PluginHelper::extensionComponentsViewPath($uniKey);

        // 删除插件目录
        (new Filesystem)->deleteDirectory($pluginAllPath);
        // 创建插件目录
        (new Filesystem)->ensureDirectoryExists($pluginAllPath);
        // 复制插件到插件目录
        (new Filesystem)->copyDirectory($extensionAllPath, $pluginAllPath);
        // 插件静态文件
        (new Filesystem)->copyDirectory($extensionAssetsPath, $frameworkAssetsPath);
        // 插件视图组件
        (new Filesystem)->copyDirectory($extensionComponentsAppPath, $frameworkComponentsAppPath);
        // 插件视图组件
        (new Filesystem)->copyDirectory($extensionComponentsViewPath, $frameworkComponentsViewPath);

        // 删除插件中需要分发的文件
        $deleteRuntimeDirArr = ['assets', 'views', 'components', 'lang', 'LICENSE'];
        foreach ($deleteRuntimeDirArr as $subDir) {
            $delSubDir = implode(DIRECTORY_SEPARATOR, [$pluginAllPath, $subDir]);
            if (is_dir($delSubDir)) {
                File::deleteDirectory($delSubDir);
            }
            if (is_file($delSubDir)) {
                File::delete($delSubDir);
            }
        }

        // 初始化文件加载
        InstallHelper::freshSystem();

        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $type = $pluginConfig->type;

        // extension 信息
        $extensionViewPath = PluginHelper::extensionViewPath($uniKey);
        LogService::info('extensionViewPath', $extensionViewPath);
        // 主题模版
        if ($type == PluginConst::PLUGIN_TYPE_THEME) {
            $frameworkThemePath = PluginHelper::frameworkThemePath($uniKey);
            LogService::info('frameworkThemePath', $frameworkThemePath);
            self::copyPluginDirectory($extensionViewPath, $frameworkThemePath);
        } else {
            // views 插件试图文件，直接分发至框架 views目录下, 包括设置文件
            $frameworkViewPath = PluginHelper::frameworkViewPath($uniKey);
            self::copyPluginDirectory($extensionViewPath, $frameworkViewPath);

            $info['extension_view'] = $extensionViewPath;
            $info['framework_view'] = $frameworkViewPath;

            // lang
            self::pushLang($uniKey);
        }
    }

    // 语言文件同步
    public static function pushLang($uniKey)
    {
        $info = [];
        $extensionLangPath = PluginHelper::extensionLangPath($uniKey);
        $frameworkLangPath = PluginHelper::frameworkLangPath($uniKey);

        if (! is_dir($extensionLangPath)) {
            LogService::info('没有语言路径');

            return;
        }

        $dir = new \DirectoryIterator($extensionLangPath);
        foreach ($dir as $file) {
            // 遍历子目录
            if ($file->isDir()) {
                $fileName = $file->getFilename();
                if (in_array($fileName, PluginConst::PLUGIN_SKIP_DIR_ARR)) {
                    continue;
                }
                $filePath = $file->getPath();

                $extensionSubLangPath = implode(DIRECTORY_SEPARATOR, [$extensionLangPath, $fileName]);

                $frameworkSubLangPath = implode(DIRECTORY_SEPARATOR, [$frameworkLangPath, $fileName, $uniKey]);

                $info['extension_sub_lang_path'] = $extensionSubLangPath;
                $info['framework_sub_lang_path'] = $frameworkSubLangPath;
                LogService::info('curr', $info);

                // extension -> framework
                if (is_dir($extensionSubLangPath)) {
                    self::copyPluginDirectory($extensionSubLangPath, $frameworkSubLangPath);
                }
            }
        }
    }

    // 删除插件文件和目录
    public static function deletePluginFiles($uniKey)
    {
        $info = [];
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $type = $pluginConfig->type;

        // 运行主目录
        $runtimeAllPath = self::getPluginRuntimePath($uniKey);

        // views 插件试图文件，直接分发至框架 views目录下, 包括设置文件
        $frameworkViewPath = PluginHelper::frameworkViewPath($uniKey);

        $frameworkThemePath = PluginHelper::frameworkThemePath($uniKey);

        // 语言目录
        self::deleteLang($uniKey);

        $info['framework_view'] = $frameworkViewPath;
        $info['framework_theme_path'] = $frameworkThemePath;
        $info['runtime_all_path'] = $runtimeAllPath;

        foreach ($info as $key => $path) {
            if (is_dir($path)) {
                File::deleteDirectory($path);
            }
        }

        InstallHelper::freshSystem();
    }

    // 语言文件删除
    public static function deleteLang($uniKey)
    {
        $info = [];
        $srcPath = PluginHelper::frameworkLangPath($uniKey);
        $info['lang_path_framework'] = $srcPath;

        $dir = new \DirectoryIterator($srcPath);
        foreach ($dir as $file) {
            // 遍历子目录
            if ($file->isDir()) {
                $fileName = $file->getFilename();
                if (in_array($fileName, PluginConst::PLUGIN_SKIP_DIR_ARR)) {
                    continue;
                }

                $filePath = $file->getPath();
                $frameworkLangPath = implode(DIRECTORY_SEPARATOR, [$filePath, $fileName, $uniKey]);

                if (is_dir($frameworkLangPath)) {
                    File::deleteDirectory($frameworkLangPath);
                }
            }
        }
    }

    //  所有插件安装之前都在这个目录下
    public static function getExtensionsRootPath()
    {
        $pathArr = [base_path(), 'extensions'];
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        return $path;
    }

    // 获取插件本地路径
    public static function getPluginExtensionPath($dirName)
    {
        $pathArr = [base_path(), 'extensions', $dirName];
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        return $path;
    }

    // 获取插件本地路径
    public static function getPluginRuntimePath($dirName)
    {
        $pathArr = [base_path(), 'app', 'Plugins', $dirName];
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        return $path;
    }

    public static function freshSystem()
    {
        $composer = app('composer');
        $composer->dumpAutoloads();

        Artisan::call('clear-compiled'); // Remove the compiled class file
        // 删除缓存文件
        $deleteDir = implode(DIRECTORY_SEPARATOR, [base_path(), 'bootstrap', 'cache']);
        $deleteFileArr = [
            'config.php',
            'packages.php',
            'services.php',
            'route.php',
        ];
        foreach ($deleteFileArr as $file) {
            $deleteFile = implode(DIRECTORY_SEPARATOR, [$deleteDir, $file]);
            if (is_file($deleteFile)) {
                File::delete($deleteFile);
            }
        }
    }
}
