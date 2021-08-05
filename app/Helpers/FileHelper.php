<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Http\Models\Common\AssetFile;
use App\Http\Share\Common\LogService;
use Illuminate\Support\Facades\File;

class FileHelper
{

    // 获取目录下的所有文件
    public static function getAllFiles($path, &$files, $ignoreExtensionArr = []) {
        if(is_dir($path)){
            $dp = dir($path);
            while ($file = $dp ->read()){
                if($file !="." && $file !=".."){
                    self::getAllFiles($path."/".$file, $files, $ignoreExtensionArr);
                }
            }
            $dp ->close();
        }
        if(is_file($path)){
            $extension = pathinfo($path)['extension'];
            if(in_array($extension, $ignoreExtensionArr)){
                return;
            }

            $files[] =  $path;
        }
    }


    // 获取目录下所有目录
    public static function getAllFolders($path, &$files, $ignoreArr = []) {
        if(is_dir($path)){
            $dp = dir($path);
            while ($file = $dp ->read()){
                if($file !="." && $file !=".."){
                    $files[] =  $path;
                    $files = array_unique($files);
                    self::getAllFolders($path."/".$file, $files, $ignoreArr);
                }
            }
            $dp ->close();
        }
    }

    // 获取 rootPath 第二层目录
    public static function getFolderDepth($folders, $rootPath, $depth){
        $newFolders = [];
        foreach ($folders as $folder){
            $suffix = str_replace($rootPath, '', $folder);
            $suffixArr = explode(DIRECTORY_SEPARATOR, $suffix);
            if(is_array($suffixArr) && count($suffixArr) == $depth){
                $newFolders[] = str_replace($rootPath, '', $folder);
            }
        }

        return $newFolders;
    }


    // 图片转本地，防止跨域
    public static function fileItemToLocalDomain($ossUrl, $subFolder = 'avatars'){

        // 准备数据
        $fileItem = AssetFile::staticFindByField('oss_url', $ossUrl);
        $domain = CommonHelper::domain();

        // 如果没有查到, 则直接拼接
        if(empty($fileItem)){
            $urlArr = explode('/', $ossUrl);
            $fileName = end($urlArr);
            // 注: 默认图片在 avatar下面
            $localUrl = implode("/", [$domain, 'storage', $subFolder, $fileName]);
            return $localUrl;
        }

        // 如果查到
        $uri = $fileItem->uri;

        $localUrl = $domain . $uri;

        return $localUrl;
    }

    //通过oss_url链接查询本地图片
    public static function getLocalUrl($ossUrl)
    {
        $domain = CommonHelper::domain();

        $url = $domain . '/storage/avatars/';

        $urlArr = explode('/', $ossUrl);
        $fileName = end($urlArr);

        $url = $url . $fileName;

        if(empty($fileName)){
            return $ossUrl;
        }

        return $url;

    }

    // 确认目录存在，不存在则创建
    public static function assetDir($path){
        if(is_dir($path)){
            return true;
        }
        return File::makeDirectory($path, 0777, true, true);
    }


    /**
     * 解压文件
     * @param $fromName 被解压的文件名
     * @param $toName 解压到哪个目录下
     * @return false  成功返回TRUE, 失败返回FALSE
     */
    public static function unzip($fromName, $toName)
    {
        if(!file_exists($fromName)){
            LogService::info("文件不存在", $fromName);
            return FALSE;
        }
        $zipArc = new \ZipArchive();
        if(!$zipArc->open($fromName)){
            LogService::info("文件不存在");
            return FALSE;
        }
        if(!$zipArc->extractTo($toName)){
            $zipArc->close();
            return FALSE;
        }
        return $zipArc->close();
    }

    /**
     * 这个函数里面调试要用 var_dump
     * @param $zipFilename
     * @param $path
     * @param $toPath
     */
    public static function zip($zipFilename, $folderName, $path, $toPath){

        $zip = new \ZipArchive();
        $zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name =>  $file) {
            // 我们要跳过所有子目录
            if (!$file->isDir()) {
                $filePath  = $file->getRealPath();
             //   var_dump($filePath);

                // 用 substr/strlen 获取文件扩展名
                $relativePath = "{$folderName}/" . substr($filePath, strlen($path) + 1);
             //   var_dump($relativePath);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        return true;
    }
        /**
     * 将一个文件夹下的所有文件及文件夹
     * 复制到另一个文件夹里（保持原有结构）
     *
     * @param <string> $rootFrom 源文件夹地址
     * @param <string> $rootTo 目的文件夹地址
     */
    public static function cp_files($rootFrom,$rootTo){
        $handle = opendir($rootFrom);
        while(false  !== ($file = readdir($handle))){
            //DIRECTORY_SEPARATOR 为系统的文件夹名称的分隔符 例如：windos为'/'; linux为'/'
            $fileFrom = $rootFrom.DIRECTORY_SEPARATOR.$file;
            $fileTo = $rootTo.DIRECTORY_SEPARATOR.$file;
            if($file =='.' || $file=='..'){
                continue;
            }
            if(is_dir($fileFrom)){
                mkdir($fileTo,0777,true,true);
                self::cp_files($fileFrom,$fileTo);
            }else{
                @copy($fileFrom,$fileTo);
                if(strstr($fileTo,"access_token.txt")){
                    chmod($fileTo, 0777);
                }
            }
        }
    }

}
