<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Scene;

class FileSceneConfig
{

    CONST FILE_TYPE_DEFAULT = 'file_default';   // 默认
    CONST FILE_TYPE_IMAGE   = 'file_image'; // 图片
    CONST FILE_TYPE_VIDEO   = 'file_video'; // 视频
    CONST FILE_TYPE_AUDIO   = 'file_audio'; // 音频
    CONST FILE_TYPE_DOC     = 'file_doc';   // 文档
    CONST FILE_TYPE_CONFIG  = 'file_config';    // 配置类

    //文件类型
    CONST FILE_TYPE_1 = 1; //图片
    CONST FILE_TYPE_2 = 2; //视频
    CONST FILE_TYPE_3 = 3; //音频
    CONST FILE_TYPE_4 = 4; //文档

    //来源类型
    CONST TABLE_TYPE_1 = 1;
    CONST TABLE_TYPE_2 = 2;
    CONST TABLE_TYPE_3 = 3;
    CONST TABLE_TYPE_4 = 4;
    CONST TABLE_TYPE_5 = 5;
    CONST TABLE_TYPE_6 = 6;
    CONST TABLE_TYPE_7 = 7;
    CONST TABLE_TYPE_8 = 8;
    CONST TABLE_TYPE_9 = 9;
    CONST TABLE_TYPE_10 = 10;
    CONST TABLE_TYPE_11 = 11;

    // 上传文件
    CONST UPLOAD = 'upload';

    // 上传 base64文件
    CONST UPLOAD_BASE64 = 'upload_base64';

    // 上传二进制文件
    CONST UPLOAD_BLOB = 'upload_blob';

    // 上传模式
    CONST UPLOAD_PROVIDER_LOCAL = 'local';
    CONST UPLOAD_PROVIDER_PLUGIN = 'plugin';

    // 规则 - 文件上传
    public function uploadRule(){
        $rule = [
            'file'    => 'required|file',
            'file_type'       => 'required|min:2',
        ];
        return $rule;
    }

    // 规则 - 文件上传 base64
    public function uploadBase64Rule(){
        $rule = [
            'fileBase64'    => 'required|min:30',
            'file_type'       => 'required|min:2',
        ];
        return $rule;
    }


}
