<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Scene;

class FileSceneConfig
{
    const FILE_TYPE_DEFAULT = 'file_default';   // 默认
    const FILE_TYPE_IMAGE = 'file_image'; // 图片
    const FILE_TYPE_VIDEO = 'file_video'; // 视频
    const FILE_TYPE_AUDIO = 'file_audio'; // 音频
    const FILE_TYPE_DOC = 'file_doc';   // 文档
    const FILE_TYPE_CONFIG = 'file_config';    // 配置类

    //文件类型
    const FILE_TYPE_1 = 1; //图片
    const FILE_TYPE_2 = 2; //视频
    const FILE_TYPE_3 = 3; //音频
    const FILE_TYPE_4 = 4; //文档

    //来源类型
    const TABLE_TYPE_1 = 1;
    const TABLE_TYPE_2 = 2;
    const TABLE_TYPE_3 = 3;
    const TABLE_TYPE_4 = 4;
    const TABLE_TYPE_5 = 5;
    const TABLE_TYPE_6 = 6;
    const TABLE_TYPE_7 = 7;
    const TABLE_TYPE_8 = 8;
    const TABLE_TYPE_9 = 9;
    const TABLE_TYPE_10 = 10;
    const TABLE_TYPE_11 = 11;

    // 上传文件
    const UPLOAD = 'upload';

    // 上传 base64文件
    const UPLOAD_BASE64 = 'upload_base64';

    // 上传二进制文件
    const UPLOAD_BLOB = 'upload_blob';

    // 上传模式
    const UPLOAD_PROVIDER_LOCAL = 'local';
    const UPLOAD_PROVIDER_PLUGIN = 'plugin';

    // 规则 - 文件上传
    public function uploadRule()
    {
        $rule = [
            'file'    => 'required|file',
            'file_type'       => 'required|min:2',
        ];

        return $rule;
    }

    // 规则 - 文件上传 base64
    public function uploadBase64Rule()
    {
        $rule = [
            'fileBase64'    => 'required|min:30',
            'file_type'       => 'required|min:2',
        ];

        return $rule;
    }
}
