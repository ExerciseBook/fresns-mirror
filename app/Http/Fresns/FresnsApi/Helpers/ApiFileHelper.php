<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Helpers;

use App\Http\Center\Helper\PluginHelper as HelperPluginHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsCmds\FresnsPlugin as FresnsCmdsFresnsPlugin;
use App\Http\Fresns\FresnsCmds\FresnsPluginConfig;
use App\Http\Fresns\FresnsDialogMessages\FresnsDialogMessages;
use App\Http\Fresns\FresnsMembers\FresnsMembersConfig;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsFileAppends\FresnsFileAppends;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsApi\Content\AmConfig as ContentConfig;
use App\Http\Share\Common\LogService;
use App\Plugins\AliOss\PluginConfig;

class ApiFileHelper
{
    public static function getFileInfo($messageId, $file_id, $mid)
    {
        $messageInfo = FresnsDialogMessages::find($messageId);
        $fileInfo = FresnsFiles::find($file_id);
        $fileAppend = FresnsFileAppends::findAppend('file_id', $file_id);

        $fileArr['messageId'] = $messageInfo['id'];
        $fileArr['isMe'] = $messageInfo['send_member_id'] == $mid ? true : false;
        $fileArr['type'] = "附件消息";
        $file = [];
        if ($fileInfo) {
            $file['fileId'] = $file_id;
            $file['fileType'] = $fileInfo['file_type'];
            $file['fileName'] = $fileInfo['file_name'];
            $file['fileExtension'] = $fileInfo['file_extension'];
            $file['fileSize'] = $fileAppend['file_extension'] ?? "";
            $file['imageWidth'] = $fileAppend['image_width'] ?? "";
            $file['imageHeight'] = $fileAppend['image_height'] ?? "";
            // 图片类型
            $file['imageThumbUrl'] = "";
            $file['imageSquareUrl'] = "";
            $file['imageBigUrl'] = "";
            // $img_setting = ApiConfigHelper::getConfigByKey(AmConfig::IMG_SETTING);
            // dd($img_setting);
            $imagesHost = ApiConfigHelper::getConfigByItemKey('images_bucket_domain');
            $imagesRatio = ApiConfigHelper::getConfigByItemKey('images_thumb_ratio');
            $imagesSquare = ApiConfigHelper::getConfigByItemKey('images_thumb_square');
            $imagesBig = ApiConfigHelper::getConfigByItemKey('images_thumb_big');
            // 图片类型
            if ($fileInfo['file_type'] == 1) {
                $file['imageLong'] = $fileAppend['image_is_long'] ?? "";
                $file['imageThumbUrl'] = $imagesHost.$fileInfo['file_path'].$imagesRatio;
                $file['imageSquareUrl'] = $imagesRatio.$fileInfo['file_path'].$imagesSquare;
                $file['imageBigUrl'] = $imagesSquare.$fileInfo['file_path'].$imagesBig;
            }
            // 视频类型
            // $file['videoTime'] = "";
            // $file['videoCover'] = "";
            // $file['videoGif'] = "";
            // $file['videoUrl'] = "";
            $video_setting = ApiConfigHelper::getConfigByKey(AmConfig::VIDEO_SETTING);
            // dd($video_setting);
            // 视频专用
            if ($fileInfo['file_type'] == 2) {
                $file['videoTime'] = $fileInfo['video_time'];
                $file['videoCover'] = $fileInfo['video_cover'];
                $file['videoGif'] = $fileInfo['video_gif'];
                $file['videoUrl'] = $video_setting['videos_bucket_domain'].$fileInfo['file_path'];
            }
            // 音频类型
            // $file['audioTime'] = "";
            // $file['audioUrl'] = "";
            $audio_setting = ApiConfigHelper::getConfigByKey(AmConfig::AUDIO_SETTING);
            if ($fileInfo['file_type'] == 3) {
                $file['audioTime'] = $fileInfo['audio_time'];
                $file['audioUrl'] = $audio_setting['audios_bucket_domain'].$fileInfo['file_path'];
            }
            // 文档类型
            // $file['docPreviewUrl'] = "";
            // $file['docUrl'] = "";
            $file_setting = ApiConfigHelper::getConfigByKey(AmConfig::FILE_SETTING);
            // 文档专用
            if ($fileInfo['file_type'] == 4) {
                $file['docPreviewUrl'] = $file_setting['docs_online_preview'].$file_setting['docs_bucket_domain'].$fileInfo['file_path'];
                $file['docUrl'] = $file_setting['docs_bucket_domain'].$fileInfo['file_path'];
            }

            $file['moreJson'] = [];
        }
        $fileArr['file'] = $file;
        // dd($messageInfo);
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $messageInfo['send_member_id'])->first();
        // dd($memberInfo);
        $sendDeactivate = true;
        $sendMid = $messageInfo['send_member_id'];
        if (($memberInfo->deleted_at != null)) {
            $sendMid = "";
            $sendDeactivate = false;
        }
        $fileArr['sendDeactivate'] = $sendDeactivate;
        $fileArr['sendMid'] = $sendMid;
        $fileArr['sendAvatar'] = $memberInfo->avatar_file_url;
        // 为空用默认头像
        if (empty($memberInfo->avatar_file_url)) {
            $defaultIcon = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEFAULT_AVATAR);
            $fileArr['sendAvatar'] = $defaultIcon;
        }
        // 已注销头像 deactivate_avatar 键值"
        if ($memberInfo) {
            if ($memberInfo->deleted_at != null) {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
                $fileArr['sendAvatar'] = $deactivateAvatar;
            }
        } else {
            $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
            $fileArr['sendAvatar'] = $deactivateAvatar;
        }
        $fileArr['sendTime'] = $messageInfo['created_at'];
        return $fileArr;
    }

    // 通过file表获取file信息
    public static function getFileInfoByTable($table, $table_id)
    {
        // dump($table);
        // dd($table_id);
        $fileIdArr = FresnsFiles::where('table_name', $table)->where('table_id', $table_id)->get()->toArray();
        // $fileInfo = TweetFiles::whereIn('id',$fileIdArr)->get()->toArray();
        $result = [];
        if ($fileIdArr) {
            $file = [];
            foreach ($fileIdArr as $v) {
                $fileAppend = FresnsFileAppends::findAppend('file_id', $v['id']);
                $file['fid'] = $v['uuid'];
                $file['type'] = $v['file_type'];
                $file['name'] = $v['file_name'];
                $file['extension'] = $v['file_extension'];
                $file['fileSize'] = $fileAppend['file_extension'] ?? "";
                $file['imageWidth'] = $fileAppend['image_width'] ?? "";
                $file['imageHeight'] = $fileAppend['image_height'] ?? "";
                $img_setting = ApiConfigHelper::getConfigByKey(AmConfig::IMG_SETTING);
                // dd($img_setting);
                // 图片类型
                if ($v['file_type'] == 1) {
                    $file['imageLong'] = $fileAppend['image_is_long'] ?? "";
                    $file['imageThumbUrl'] = $img_setting['images_bucket_domain'].$v['file_path'].$img_setting['images_thumb_ratio'];
                    $file['imageSquareUrl'] = $img_setting['images_bucket_domain'].$v['file_path'].$img_setting['images_thumb_square'];
                    $file['imageBigUrl'] = $img_setting['images_bucket_domain'].$v['file_path'].$img_setting['images_thumb_big'];
                }
                // 视频类型
                // $file['videoTime'] = "";
                // $file['videoCover'] = "";
                // $file['videoGif'] = "";
                // $file['videoUrl'] = "";
                $video_setting = ApiConfigHelper::getConfigByKey(AmConfig::VIDEO_SETTING);
                // dd($video_setting);
                // 视频专用
                if ($v['file_type'] == 2) {
                    $file['videoTime'] = $fileAppend['video_time'];
                    $file['videoCover'] = $fileAppend['video_cover'];
                    $file['videoGif'] = $fileAppend['video_gif'];
                    $file['videoUrl'] = $video_setting['videos_bucket_domain'].$v['file_path'];
                }
                // 音频类型          
                // $file['audioTime'] = "";
                // $file['audioUrl'] = "";
                $audio_setting = ApiConfigHelper::getConfigByKey(AmConfig::AUDIO_SETTING);
                if ($v['file_type'] == 3) {
                    $file['audioTime'] = $fileAppend['audio_time'];
                    $file['audioUrl'] = $audio_setting['audios_bucket_domain'].$v['file_path'];
                }
                // 文档类型
                // $file['docPreviewUrl'] = "";
                // $file['docUrl'] = "";
                $file_setting = ApiConfigHelper::getConfigByKey(AmConfig::FILE_SETTING);
                // 文档专用
                if ($v['file_type'] == 4) {
                    $file['docPreviewUrl'] = $file_setting['docs_online_preview'].$file_setting['docs_bucket_domain'].$v['file_path'];
                    $file['docUrl'] = $file_setting['docs_bucket_domain'].$v['file_path'];
                }
                $file['more_json'] = [];
                $result[] = $file;
            }
        }
        return $result;
    }

    // 图片（防盗链用）
    public static function antiTheftFile($fileInfo)
    {
        // $fileInfo = FresnsFiles::find($file_id);
        if ($fileInfo) {
            $files = [];
            foreach ($fileInfo as $f) {
                // $img_setting = ApiConfigHelper::getConfigByKey(AmConfig::IMG_SETTING);
                $imagesHost = ApiConfigHelper::getConfigByItemKey('images_bucket_domain');
                $imagesRatio = ApiConfigHelper::getConfigByItemKey('images_thumb_ratio');
                $imagesSquare = ApiConfigHelper::getConfigByItemKey('images_thumb_square');
                $imagesBig = ApiConfigHelper::getConfigByItemKey('images_thumb_big');
                $file = [];
                $file['imageRatioUrl'] = $imagesHost.$f['file_path'].$imagesRatio;
                $file['imageSquareUrl'] = $imagesHost.$f['file_path'].$imagesSquare;
                $file['imageBigUrl'] = $imagesHost.$f['file_path'].$imagesBig;
                $file['imageRatioUrl'] = self::getImageSignUrl($file['imageRatioUrl'] );
                $file['imageSquareUrl'] = self::getImageSignUrl($file['imageSquareUrl'] );
                $file['imageBigUrl'] = self::getImageSignUrl($file['imageBigUrl'] );
                // $file['imageThumbUrl'] = $img_setting['images_bucket_domain'].$f['file_path'].$img_setting['images_thumb_ratio'];
                // $file['imageSquareUrl'] = $img_setting['images_bucket_domain'].$f['file_path'].$img_setting['images_thumb_square'];
                // $file['imageBigUrl'] = $img_setting['images_bucket_domain'].$f['file_path'].$img_setting['images_thumb_big'];
                $files[] = $file;
            }
        }

        return $files;
    }

    //获取防盗链
    public static function signUrl($ossUrl, $replaceUrl = "http://ff973002.oss-cn-beijing.aliyuncs.com/", $timeout = 60)
    {
        $unikey = ApiConfigHelper::getConfigByItemKey('images_service');

        $pluginUniKey = $unikey;
        $pluginClass = HelperPluginHelper::findPluginClass($pluginUniKey);

        if (empty($pluginClass)) {
            LogService::error("未找到插件类");
            return false;
        }

        $isPlugin = HelperPluginHelper::pluginCanUse($pluginUniKey);

        if ($isPlugin == false) {
            LogService::error("未找到插件类");
            return false;
        }
        $cmd = PluginConfig::PLG_CMD_ANTI_LINK;
        $input = [];
        $input['url'] = $ossUrl;
        $input['replaceUrl'] = $replaceUrl;
        $input['timeout'] = $timeout;
        $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);

        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            return false;
        }

        $output = $resp['output'];

        $data = [
            'oss_url' => $output['oss_url'],
            'signed_url' => $output['signed_url'],
            'final_url' => $output['final_url'],
        ];

        return $data;
    }

    //获取单个图片的防盗链
    public static function getImageSignUrl($url)
    {
        //判断是否是id，如果是id则去数据库查询，如果不是id则直接返回
        if(!is_numeric($url)){
            $singUrl = $url;
        } else {
            $uuid = FresnsFiles::where('id',$url)->value('uuid');
            $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_IMAGE;
            $input['fid'] = $uuid;

            $resp = PluginRpcHelper::call(FresnsCmdsFresnsPlugin::class, $cmd, $input);
            if (PluginRpcHelper::isErrorPluginResp($resp)) {
                return false;
            }
            $singUrl = $resp['output']['imageDefaultUrl'];
        }
        
        return $singUrl;
    }

     //获取单个视频的防盗链
     public static function getVideoSignUrl($url)
     {
         //判断是否开启防盗链
         $imageStatus = ApiConfigHelper::getConfigByItemKey('video_url_status');
         if ($imageStatus == true && !empty($url)) {
             $imagesBucketDomain = ApiConfigHelper::getConfigByItemKey('video_bucket_domain');
             if(!strstr($imagesBucketDomain,$url)){
                $url = $imagesBucketDomain . $url;
             }
             $url = str_replace($imagesBucketDomain.'/', '', $url);
             $data = self::signUrl($url, $imagesBucketDomain);
             $singUrl = $data['signed_url'];
         } else {
             $singUrl = $url;
         }
         return $singUrl;
     }
     //获取单个音频的防盗链
     public static function getAudioSignUrl($url)
     {
         //判断是否开启防盗链
         $imageStatus = ApiConfigHelper::getConfigByItemKey('audios_url_status');
         if ($imageStatus == true && !empty($url)) {
             $imagesBucketDomain = ApiConfigHelper::getConfigByItemKey('audios_bucket_domain');
             if(!strstr($imagesBucketDomain,$url)){
                $url = $imagesBucketDomain . $url;
             }
             $url = str_replace($imagesBucketDomain.'/', '', $url);
             $data = self::signUrl($url, $imagesBucketDomain);
             $singUrl = $data['signed_url'];
         } else {
             $singUrl = $url;
         }
         return $singUrl;
     }
     //获取单个文档的防盗链
     public static function getDocsSignUrl($url)
     {
         //判断是否开启防盗链
         $imageStatus = ApiConfigHelper::getConfigByItemKey('docs_url_status');
         if ($imageStatus == true && !empty($url)) {
             $imagesBucketDomain = ApiConfigHelper::getConfigByItemKey('docs_bucket_domain');
             if(!strstr($imagesBucketDomain,$url)){
                $url = $imagesBucketDomain . $url;
             }
             $url = str_replace($imagesBucketDomain.'/', '', $url);
             $data = self::signUrl($url, $imagesBucketDomain);
             $singUrl = $data['signed_url'];
         } else {
             $singUrl = $url;
         }
         return $singUrl;
     }
    //通过文件id获取图片防盗链
    public static function getImageSignUrlByFileId($fileId)
    {
        $uuid = FresnsFiles::where('id',$fileId)->value('uuid');
        $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_IMAGE;
        $input['fid'] = $uuid;

        $resp = PluginRpcHelper::call(FresnsCmdsFresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            return false;
        }
        $singUrl = $resp['output']['imageDefaultUrl'];

        return $singUrl;
    }

    /**
     * 部分表使用获取防盗链链接
     * 1、先判断配置表 images_url_status 键名是否开启了防盗链功能。
     * 2、键值为 false 代表未开启，直接输出 file_url 字段。
     * 3、键值为 true 代表开启，则需要特殊处理，判断 file_id 是否有值。
     * 3.1、无值则直接输出 file_url 字段。
     * 3.2、有值，则代表是文件 ID，任 ID 跟插件索要 URL 信息（插件配置为 images_service 键名）
     */
    public static function getImageSignUrlByFileIdUrl($fileId, $fileUrl)
    {
        //判断是否开启防盗链
        $imageStatus = ApiConfigHelper::getConfigByItemKey('images_url_status');
        if ($imageStatus == true) {
            if (empty($fileId)) {
                return $fileUrl;
            }
            $uuid = FresnsFiles::where('id',$fileId)->value('uuid');
            $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_IMAGE;
            $input['fid'] = $uuid;

            $resp = PluginRpcHelper::call(FresnsCmdsFresnsPlugin::class, $cmd, $input);
            if (PluginRpcHelper::isErrorPluginResp($resp)) {
                return false;
            }
            return $resp['output']['imageDefaultUrl'];
        } else {
            return $fileUrl;
        }
    }

    // 获取pluginUseges url
    public static function getPluginUsagesUrl($pluginUnikey, $pluginUsagesid)
    {
        $bucketDomain = ApiConfigHelper::getConfigByItemKey(AmConfig::BACKEND_DOMAIN);
        $pluginUsages = FresnsPluginUsages::find($pluginUsagesid);
        $plugin = FresnsPlugin::where('unikey', $pluginUnikey)->first();
        $url = "";
        if (!$plugin || !$pluginUsages) {
            return $url;
        }
        $access_path = $plugin['access_path'];
        $str = strstr($access_path, '{parameter}');
        if ($str) {
            $uri = str_replace('{parameter}', $pluginUsages['parameter'], $access_path);
        } else {
            $uri = $access_path;
        }
        if (empty($plugin['plugin_url'])) {
            $url = $bucketDomain.$uri;
        } else {
            $url = $plugin['plugin_domain'].$uri;
        }
        $url = self::getImageSignUrl($url);
        return $url;
    }

    // 获取more_json防盗链
    public static function getMoreJsonSignUrl($moreJson){
        if($moreJson){
            foreach($moreJson as &$m){
                if(isset($m['imageRatioUrl'])){
                    $m['imageRatioUrl'] = self::getImageSignUrl($m['imageRatioUrl']);
                    // dd($m['imageRatioUrl']);
                }
                if(isset($m['imageSquareUrl'])){
                    $m['imageSquareUrl'] = self::getImageSignUrl($m['imageSquareUrl']);
                }
                if(isset($m['imageBigUrl'])){
                    $m['imageBigUrl'] = self::getImageSignUrl($m['imageBigUrl']);
                }
                if(isset($m['videoCover'])){
                    $m['videoCover'] = self::getVideoSignUrl($m['videoCover']);
                }
                if(isset($m['videoGif'])){
                    $m['videoGif'] = self::getVideoSignUrl($m['videoGif']);
                }
                if(isset($m['videoUrl'])){
                    $m['videoUrl'] = self::getVideoSignUrl($m['videoUrl']);
                }
                if(isset($m['audioUrl'])){
                    $m['audioUrl'] = self::getAudioSignUrl($m['audioUrl']);
                }
                if(isset($m['docUrl'])){
                    $m['docUrl'] = self::getDocsSignUrl($m['docUrl']);
                }
            }
        }
        return $moreJson;
    }
}
