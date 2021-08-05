<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Scene;

use App\Base\Services\BaseService;
use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Http\Share\Common\LogService;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;

/**
 * 文件上传
 * 处理文件上传
 */
class FileSceneService extends BaseService
{

    // 入库
    public static function createFile(){

    }

    // 获取文件存储路径
    public static function getPath($options){

        $t = time();
        $ym = date("Ym",$t);
        $day = date("d",$t);
        $suffixArr = [];
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_1){
            $suffixArr = ['mores'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_2){
            $suffixArr = ['configs', 'system'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_3){
            $suffixArr = ['configs', 'operating'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_4){
            $suffixArr = ['configs', 'emojis'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_5){
            $suffixArr = ['configs', 'member'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_6){
            $suffixArr = ['avatars', $ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['images', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['images', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['images', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['images', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['images', 'plugins',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['videos', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['videos', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['videos', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['videos', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['videos', 'plugins',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['audios', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['audios', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['audios', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['audios', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['audios', 'plugins',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['docs', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['docs', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['docs', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['docs', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['docs', 'plugins',$ym, $day];
        }

        if(empty($suffixArr)){
            // 测试路径样例 /avatars/{YYYYMM}/{DD}
            $suffixArr = ['avatars', $ym, $day];
        }


        $basePathArr = [
            base_path(),
            'storage', 'app', 'public'
        ];
        $realPath = implode(DIRECTORY_SEPARATOR, array_merge($basePathArr, $suffixArr));

        // 创建目录
        if(!FileHelper::assetDir($realPath)){
            LogService::error("创建目录失败:", $realPath);
            return false;
        }

        // 拼接为
        array_unshift($suffixArr, 'public');

        return implode(DIRECTORY_SEPARATOR, $suffixArr);
    }

    //前端编辑器文件上传
    public static function getEditorPath($options){

        $t = time();
        $ym = date("Ym",$t);
        $day = date("d",$t);
        $suffixArr = [];
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_1){
            $suffixArr = ['temp_files','mores'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_2){
            $suffixArr = ['temp_files','configs', 'system'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_3){
            $suffixArr = ['temp_files','configs', 'operating'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_4){
            $suffixArr = ['temp_files','configs', 'emojis'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_5){
            $suffixArr = ['temp_files','configs', 'member'];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_6){
            $suffixArr = ['temp_files','avatars', $ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['temp_files','images', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['temp_files','images', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['temp_files','images', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['temp_files','images', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_1 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['temp_files','images', 'plugins',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['temp_files','videos', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['temp_files','videos', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['temp_files','videos', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['temp_files','videos', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_2 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['temp_files','videos', 'plugins',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['temp_files','audios', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['temp_files','audios', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['temp_files','audios', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['temp_files','audios', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_3 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['temp_files','audios', 'plugins',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_7){
            $suffixArr = ['temp_files','docs', 'dialogs',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_8){
            $suffixArr = ['temp_files','docs', 'posts',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_9){
            $suffixArr = ['temp_files','docs', 'comments',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_10){
            $suffixArr = ['temp_files','docs', 'extends',$ym, $day];
        }
        if($options['file_type'] == FileSceneConfig::FILE_TYPE_4 && $options['table_type'] == FileSceneConfig::TABLE_TYPE_11){
            $suffixArr = ['temp_files','docs', 'plugins',$ym, $day];
        }

        if(empty($suffixArr)){
            // 测试路径样例 /avatars/{YYYYMM}/{DD}
            $suffixArr = ['avatars', $ym, $day];
        }


        $basePathArr = [
            base_path(),
            'storage', 'app', 'public'
        ];
        $realPath = implode(DIRECTORY_SEPARATOR, array_merge($basePathArr, $suffixArr));

        // 创建目录
        if(!FileHelper::assetDir($realPath)){
            LogService::error("创建目录失败:", $realPath);
            return false;
        }

        // 拼接为
        array_unshift($suffixArr, 'public');

        return implode(DIRECTORY_SEPARATOR, $suffixArr);
    }


    // 获取存储服务商, 本地上传或者插件上传
    // todo 根据后台配置判断
    public static function getUploadProvider(){

       return FileSceneConfig::UPLOAD_PROVIDER_PLUGIN;
    }

    public static function getFileUrl($fileUrl){

        $domain = ApiCommonHelper::domain();

        $url = $domain . "/storage" . $fileUrl;

        return $url;
    }

}
