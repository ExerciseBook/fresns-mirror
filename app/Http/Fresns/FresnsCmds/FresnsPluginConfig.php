<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsCmds;

use App\Http\Center\Base\BasePluginConfig;
use Illuminate\Validation\Rule;

class FresnsPluginConfig extends BasePluginConfig
{
    // unikey
    public $uniKey = 'fresns';

    // 插件默认命令字, 任何插件必须要要有
    public const PLG_CMD_DEFAULT = 'plg_cmd_default';

    // 发送验证码命令字
    public const PLG_CMD_SEND_CODE = 'plg_cmd_send_code';

    // 校验验证码
    public const PLG_CMD_CHECKED_CODE = 'plg_cmd_checked_code';

    //帖子草稿入库
    // public CONST PLG_CMD_POST_LOG_CREATED = 'plg_cmd_post_log_created';
    public const PLG_CMD_DIRECT_RELEASE_CONTENT = 'plg_cmd_direct_release_content';

    // 发信-邮件
    public const PLG_CMD_SEND_EMAIL = 'plg_cmd_send_email';

    // 发信-手机
    public const PLG_CMD_SEND_SMS = 'plg_cmd_send_sms';

    // 发信-微信推送
    public const PLG_CMD_SEND_WECHAT = 'plg_cmd_send_wechat';

    // 	发信-iOS 推送
    public const PLG_CMD_SEND_IOS = 'plg_cmd_send_ios';

    // 	发信-Android 推送
    public const PLG_CMD_SEND_ANDROID = 'plg_cmd_send_android';

    //创建交互凭证
    public const PLG_CMD_CREATE_SESSION_TOKEN = 'plg_cmd_create_session_token';
    //校验交互凭证
    public const PLG_CMD_VERIFY_SESSION_TOKEN = 'plg_cmd_verify_session_token';
    //上传交互日志
    public const PLG_CMD_UPLOAD_SESSION_LOG = 'plg_cmd_upload_session_log';
    //获取上传凭证
    public const PLG_CMD_GET_UPLOAD_TOKEN = 'plg_cmd_get_upload_token';
    //上传文件
    public const PLG_CMD_UPLOAD_FILE = 'plg_cmd_upload_file';
    //图片存储
    public const PLG_CMD_ANTI_LINK_IMAGE = 'plg_cmd_anti_link_image';
    //视频存储
    public const PLG_CMD_ANTI_LINK_VIDEO = 'plg_cmd_anti_link_video';
    //音频存储
    public const PLG_CMD_ANTI_LINK_AUDIO = 'plg_cmd_anti_link_audio';
    //文档存储
    public const PLG_CMD_ANTI_LINK_DOC = 'plg_cmd_anti_link_doc';
    //凭fid命令删除物理文件
    public const PLG_CMD_HARD_DELETE_FID = 'plg_cmd_hard_delete_fid';
    //获取上传文件凭证token
    public const PLG_CMD_GET_TOKEN = 'plg_cmd_get_token';
    //获取上传文件网址
    public const PLG_CMD_GET_ACCESS_PATH = 'plg_cmd_get_access_path';
    //签名验证
    public const PLG_CMD_VERIFY_SIGN = 'plg_cmd_verify_sign';

    // 删除正式内容
    public const PLG_CMD_DELETE_CONTENT = 'plg_cmd_delete_content';
    //钱包收入交易
    public const PLG_CMD_WALLET_INCREASE = 'plg_cmd_wallet_increase';
    //钱包支出交易
    public const PLG_CMD_WALLET_DECREASE = 'plg_cmd_wallet_decrease';
    // 插件命令字回调映射
    const PLG_CMD_HANDLE_MAP = [
        self::PLG_CMD_DEFAULT => 'defaultHandler',
        self::PLG_CMD_SEND_CODE => 'sendCodeHandler',
        self::PLG_CMD_CHECKED_CODE => 'checkedCodeHandler',
        self::PLG_CMD_DIRECT_RELEASE_CONTENT => 'directReleaseContentHandler',
        self::PLG_CMD_SEND_EMAIL => 'sendEmailHandler',
        self::PLG_CMD_SEND_SMS => 'sendSmsHandler',
        self::PLG_CMD_SEND_WECHAT => 'sendWechatHandler',
        self::PLG_CMD_SEND_IOS => 'sendIosHandler',
        self::PLG_CMD_SEND_ANDROID => 'sendAndriodHandler',
        self::PLG_CMD_CREATE_SESSION_TOKEN => 'plgCmdCreateSessionTokenHandler',
        self::PLG_CMD_VERIFY_SESSION_TOKEN => 'plgCmdVerifySessionTokenHandler',
        self::PLG_CMD_UPLOAD_SESSION_LOG => 'plgCmdUploadSessionLogHandler',
        self::PLG_CMD_GET_UPLOAD_TOKEN => 'plgCmdGetUploadTokenHandler',
        self::PLG_CMD_UPLOAD_FILE => 'plgCmdUploadFileHandler',
        self::PLG_CMD_ANTI_LINK_IMAGE => 'plgCmdAntiLinkImageHandler',
        self::PLG_CMD_ANTI_LINK_VIDEO => 'plgCmdAntiLinkVideoHandler',
        self::PLG_CMD_ANTI_LINK_AUDIO => 'plgCmdAntiLinkAudioHandler',
        self::PLG_CMD_ANTI_LINK_DOC => 'plgCmdAntiLinkDocHandler',
        self::PLG_CMD_HARD_DELETE_FID => 'plgCmdHardDeleteFidHandler',
        self::PLG_CMD_DELETE_CONTENT => 'deleteContentHandler',
        self::PLG_CMD_GET_TOKEN => 'plgCmdGetTokenHandler',
        self::PLG_CMD_GET_ACCESS_PATH => 'plgCmdGetAccessPathHandler',
        self::PLG_CMD_VERIFY_SIGN => 'plgCmdVerifySignHandler',
        self::PLG_CMD_WALLET_INCREASE => 'plgCmdWalletIncreaseHandler',
        self::PLG_CMD_WALLET_DECREASE => 'plgCmdWalletDecreaseHandler',
    ];

    // 发送验证码
    public function sendCodeHandlerRule()
    {
        $request = request();
        $rule = [
            'type' => 'required|in:1,2',
            // 'useType' => 'required|in:1,2,3,4,5',
            'template' => 'required',
            // 'template' => 'required',
            'account' => 'required',
            'langTag' => 'required',
        ];
        // 校验参数
        // $type = $request->input('type');
        // switch ($type) {
        //     case 1:
        //         $rule = [
        //             'account' => 'required|email',
        //         ];
        //         break;

        //     case 2:
        //         $rule = [
        //             'account' => 'required|numeric|regex:/^1[^0-2]\d{9}$/',
        //         ];
        //         break;
        // }
        // $rule = [
        //     'type' => 'required|in:1,2',
        //     'useType' => 'required|in:1,2,3,4,5',
        //     'template' => 'required',
        //     // 'template' => 'required',
        //     'account' => 'required',
        //     'langTag' => 'required'
        // ];
        return $rule;
    }

    // 校验验证码
    public function checkedCodeHandlerRule()
    {
        $request = request();
        $rule = [
            'type' => 'required|in:1,2',
            'verifyCode' => 'required',
            'account' => 'required',
            // 'countryCode' => 'required',
        ];
        // // 校验参数
        // $type = $request->input('type');
        // switch ($type) {
        //     case 1:
        //         $rule = [
        //             'account' => 'required|email',
        //         ];
        //         break;

        //     case 2:
        //         $rule = [
        //             'account' => 'required|numeric|regex:/^1[^0-2]\d{9}$/',
        //         ];
        //         break;
        // }
        return $rule;
    }

    // 帖子草稿相关
    public function directReleaseContentHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2',
            'logId' => 'required',
        ];

        return $rule;
    }

    // 发送邮件
    public function sendEmailHandlerRule()
    {
        $rule = [
            'email' => 'required',
            'title' => 'required',
            'content' => 'required',
        ];

        return $rule;
    }

    // 发送手机
    public function sendSmsHandlerRule()
    {
        $rule = [
            // 'phone' => 'required|numeric|regex:/^1[^0-2]\d{9}$/',
            'phone' => 'required',
            'template' => 'required',
            'variale1' => 'required',
            'variale2' => 'required',
            'countryCode' => 'required',
        ];

        return $rule;
    }

    // 发信-微信推送
    public function sendWechatHandlerRule()
    {
        $rule = [
            'mid' => 'required',
            'template' => 'required',
            'channel' => 'required|in:1,2',
            'coverFileUrl' => 'required',
            'title' => 'required',
            'content' => 'required',
            'time' => 'required',
            'linkType' => 'required',
            'linkUrl' => 'required',
        ];

        return $rule;
    }

    // 	发信-iOS 推送
    public function sendIosHandlerRule()
    {
        $rule = [
            'mid' => 'required',
            'template' => 'required',
            'coverFileUrl' => 'required',
            'title' => 'required',
            'content' => 'required',
            'time' => 'required',
            'linkType' => 'required',
            'linkUrl' => 'required',
        ];

        return $rule;
    }

    public function sendAndriodHandlerRule()
    {
        $rule = [
            'mid' => 'required',
            'template' => 'required',
            'coverFileUrl' => 'required',
            'title' => 'required',
            'content' => 'required',
            'time' => 'required',
            'linkType' => 'required',
            'linkUrl' => 'required',
        ];

        return $rule;
    }

    //创建交互凭证
    public function plgCmdCreateSessionTokenHandlerRule()
    {
        $rule = [
            'platform' => 'required',
            'uid' => 'required',
        ];

        return $rule;
    }

    //校验交互凭证
    public function plgCmdVerifySessionTokenHandlerRule()
    {
        $rule = [
            'platform' => 'required',
            'uid' => 'required',
            'token' => 'required',
        ];

        return $rule;
    }

    //上传交互日志
    public function plgCmdUploadSessionLogHandlerRule()
    {
        $rule = [
            'platform' => 'required',
            'version' => 'required',
            'versionInt' => 'required',
            'langTag' => 'required',
            'objectName' => 'required',
            'objectAction' => 'required',
            'objectResult' => 'required',
            'deviceInfo' => 'json',
            'moreJson' => 'json',
        ];

        return $rule;
    }

    //获取上传凭证
    public function plgCmdGetUploadTokenHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2,3,4',
            'mode' => 'required|in:1,2',
            'scene' => 'required|numeric',

        ];

        return $rule;
    }

    //上传文件
    public function plgCmdUploadFileHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2,3,4',
            'tableType' => 'required',
            'tableName' => 'required',
            'tableField' => 'required',
            'mode' => 'required|in:1,2',
        ];

        return $rule;
    }

    //图片存储
    public function plgCmdAntiLinkImageHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];

        return $rule;
    }

    //视频存储
    public function plgCmdAntiLinkVideoHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];

        return $rule;
    }

    //音频存储
    public function plgCmdAntiLinkAudioHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];

        return $rule;
    }

    //文档存储
    public function plgCmdAntiLinkDocHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];

        return $rule;
    }

    //删除物理文件
    public function plgCmdHardDeleteFidHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];

        return $rule;
    }

    // 删除正式内容
    public function deleteContentHandlerRule()
    {
        $rule = [
            'type' => 'required | in:1,2',
            'content' => 'required',
        ];

        return $rule;
    }

    public function plgCmdGetTokenHandlerRule()
    {
        $rule = [
            'type' => 'required | in:1,2,3,4',
            'scene' => 'required|in:1,2,3,4,5,6,7,8,9,10,11',
        ];

        return $rule;
    }

    public function plgCmdGetAccessPathHandlerRule()
    {
        $rule = [
            'type' => 'required | in:1,2,3,4',
            'scene' => 'required|in:1,2,3,4,5,6,7,8,9,10,11',
        ];

        return $rule;
    }

    public function plgCmdVerifySignHandlerRule()
    {
        $rule = [
            'platform' => 'required',
            'appId' => 'required',
            'timestamp' => 'required',
            'sign' => 'required',
        ];

        return $rule;
    }

    public function plgCmdWalletIncreaseHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2,3',
            'uid' => 'required',
            'amount' => 'required|numeric',
            'transactionAmount' => 'required|numeric',
            'systemFee' => 'required|numeric',
            'originName' => 'required',
        ];

        return $rule;
    }

    public function plgCmdWalletDecreaseHandlerRule()
    {
        $rule = [
            'type' => 'required|in:4,5,6',
            'uid' => 'required',
            'amount' => 'required|numeric',
            'transactionAmount' => 'required|numeric',
            'systemFee' => 'required|numeric',
            'originName' => 'required',
        ];

        return $rule;
    }
}
