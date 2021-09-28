<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsCmd;

use App\Http\Center\Base\BasePluginConfig;
use Illuminate\Validation\Rule;

class FresnsPluginConfig extends BasePluginConfig
{
    /**
     * System Command Word
     * https://fresns.org/extensions/command.html
     */

    // unikey
    public $uniKey = 'fresns';

    // Plugin default command word (a must for any plugin)
    public const PLG_CMD_DEFAULT = 'plg_cmd_default';

    // Command Word: Send verification code
    public const PLG_CMD_SEND_CODE = 'plg_cmd_send_code';

    // Command Word: Verify the verification code
    public const PLG_CMD_CHECKED_CODE = 'plg_cmd_checked_code';

    // Command Word: Send email
    public const PLG_CMD_SEND_EMAIL = 'plg_cmd_send_email';

    // Command Word: Send sms
    public const PLG_CMD_SEND_SMS = 'plg_cmd_send_sms';

    // Command Word: Send wechat push
    public const PLG_CMD_SEND_WECHAT = 'plg_cmd_send_wechat';

    // Command Word: Send ios push
    public const PLG_CMD_SEND_IOS = 'plg_cmd_send_ios';

    // Command Word: Send android push
    public const PLG_CMD_SEND_ANDROID = 'plg_cmd_send_android';

    // Command Word: Verify Sign
    public const PLG_CMD_VERIFY_SIGN = 'plg_cmd_verify_sign';

    // Command Word: Creating Token
    public const PLG_CMD_CREATE_SESSION_TOKEN = 'plg_cmd_create_session_token';

    // Command Word: Verify Token
    public const PLG_CMD_VERIFY_SESSION_TOKEN = 'plg_cmd_verify_session_token';

    // Command Word: Upload log
    public const PLG_CMD_UPLOAD_SESSION_LOG = 'plg_cmd_upload_session_log';

    // Command Word: Get upload token
    public const PLG_CMD_GET_UPLOAD_TOKEN = 'plg_cmd_get_upload_token';

    // Command Word: Upload file
    public const PLG_CMD_UPLOAD_FILE = 'plg_cmd_upload_file';

    // Command Word: anti hotlinking (image)
    public const PLG_CMD_ANTI_LINK_IMAGE = 'plg_cmd_anti_link_image';

    // Command Word: anti hotlinking (video)
    public const PLG_CMD_ANTI_LINK_VIDEO = 'plg_cmd_anti_link_video';

    // Command Word: anti hotlinking (audio)
    public const PLG_CMD_ANTI_LINK_AUDIO = 'plg_cmd_anti_link_audio';

    // Command Word: anti hotlinking (doc)
    public const PLG_CMD_ANTI_LINK_DOC = 'plg_cmd_anti_link_doc';

    // Command Word: Delete physical file by fid
    public const PLG_CMD_PHYSICAL_DELETION_FILE = 'plg_cmd_physical_deletion_file';

    // Command Word: Submit content into the main form
    public const PLG_CMD_DIRECT_RELEASE_CONTENT = 'plg_cmd_direct_release_content';

    // Command Word: Delete official content
    public const PLG_CMD_DELETE_CONTENT = 'plg_cmd_delete_content';

    // Command Word: Wallet Trading (increase)
    public const PLG_CMD_WALLET_INCREASE = 'plg_cmd_wallet_increase';

    // Command Word: Wallet Trading (decrease)
    public const PLG_CMD_WALLET_DECREASE = 'plg_cmd_wallet_decrease';

    //注册
    public const PLG_CMD_USER_REGISTER = 'plg_cmd_user_register';

    //登录
    public const PLG_CMD_USER_LOGIN = 'plg_cmd_user_login';




    // Command word callback mapping
    const PLG_CMD_HANDLE_MAP = [
        self::PLG_CMD_DEFAULT => 'defaultHandler',
        self::PLG_CMD_SEND_CODE => 'sendCodeHandler',
        self::PLG_CMD_CHECKED_CODE => 'checkedCodeHandler',
        self::PLG_CMD_DIRECT_RELEASE_CONTENT => 'directReleaseContentHandler',
        self::PLG_CMD_SEND_EMAIL => 'sendEmailHandler',
        self::PLG_CMD_SEND_SMS => 'sendSmsHandler',
        self::PLG_CMD_SEND_WECHAT => 'sendWeChatHandler',
        self::PLG_CMD_SEND_IOS => 'sendIosHandler',
        self::PLG_CMD_SEND_ANDROID => 'sendAndriodHandler',
        self::PLG_CMD_VERIFY_SIGN => 'plgCmdVerifySignHandler',
        self::PLG_CMD_CREATE_SESSION_TOKEN => 'plgCmdCreateSessionTokenHandler',
        self::PLG_CMD_VERIFY_SESSION_TOKEN => 'plgCmdVerifySessionTokenHandler',
        self::PLG_CMD_UPLOAD_SESSION_LOG => 'plgCmdUploadSessionLogHandler',
        self::PLG_CMD_GET_UPLOAD_TOKEN => 'plgCmdGetUploadTokenHandler',
        self::PLG_CMD_UPLOAD_FILE => 'plgCmdUploadFileHandler',
        self::PLG_CMD_ANTI_LINK_IMAGE => 'plgCmdAntiLinkImageHandler',
        self::PLG_CMD_ANTI_LINK_VIDEO => 'plgCmdAntiLinkVideoHandler',
        self::PLG_CMD_ANTI_LINK_AUDIO => 'plgCmdAntiLinkAudioHandler',
        self::PLG_CMD_ANTI_LINK_DOC => 'plgCmdAntiLinkDocHandler',
        self::PLG_CMD_PHYSICAL_DELETION_FILE => 'plgCmdPhysicalDeletionFileHandler',
        self::PLG_CMD_DELETE_CONTENT => 'deleteContentHandler',
        self::PLG_CMD_WALLET_INCREASE => 'plgCmdWalletIncreaseHandler',
        self::PLG_CMD_WALLET_DECREASE => 'plgCmdWalletDecreaseHandler',
        self::PLG_CMD_USER_REGISTER => 'plgCmdUserRegisterHandler',
        self::PLG_CMD_USER_LOGIN => 'plgCmdUserLoginHandler',
    ];

    // Send verification code
    public function sendCodeHandlerRule()
    {
        $request = request();
        $rule = [
            'type' => 'required|in:1,2',
            'templateId' => 'required',
            'account' => 'required',
            'langTag' => 'required',
        ];
        return $rule;
    }

    // Verify the verification code
    public function checkedCodeHandlerRule()
    {
        $request = request();
        $rule = [
            'type' => 'required|in:1,2',
            'verifyCode' => 'required',
            'account' => 'required',
        ];
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

    // Send email
    public function sendEmailHandlerRule()
    {
        $rule = [
            'email' => 'required',
            'title' => 'required',
            'content' => 'required',
        ];
        return $rule;
    }

    // Send sms
    public function sendSmsHandlerRule()
    {
        $rule = [
            'countryCode' => 'required',
            'phoneNumber' => 'required',
            'templateCode' => 'required',
            'templateParam' => 'json',
        ];
        return $rule;
    }

    // Send wechat push
    public function sendWeChatHandlerRule()
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

    // Send ios push
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

    // Send android push
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

    // Verify Sign
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

    // Creating Token
    public function plgCmdCreateSessionTokenHandlerRule()
    {
        $rule = [
            'platform' => 'required',
            'uid' => 'required',
        ];
        return $rule;
    }

    // Verify Token
    public function plgCmdVerifySessionTokenHandlerRule()
    {
        $rule = [
            'platform' => 'required',
            'uid' => 'required',
            'token' => 'required',
        ];
        return $rule;
    }

    // Upload log
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

    // Get upload token
    public function plgCmdGetUploadTokenHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2,3,4',
            'scene' => 'required|numeric',
        ];
        return $rule;
    }

    // Upload file
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

    // anti hotlinking (image)
    public function plgCmdAntiLinkImageHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];
        return $rule;
    }

    // anti hotlinking (video)
    public function plgCmdAntiLinkVideoHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];
        return $rule;
    }

    // anti hotlinking (audio)
    public function plgCmdAntiLinkAudioHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];
        return $rule;
    }

    // anti hotlinking (doc)
    public function plgCmdAntiLinkDocHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];
        return $rule;
    }

    // Delete physical file by fid
    public function plgCmdPhysicalDeletionFileHandlerRule()
    {
        $rule = [
            'fid' => 'required',
        ];
        return $rule;
    }

    // Delete official content
    public function deleteContentHandlerRule()
    {
        $rule = [
            'type' => 'required | in:1,2',
            'content' => 'required',
        ];
        return $rule;
    }

    // Wallet Trading (increase)
    public function plgCmdWalletIncreaseHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2,3',
            'uid' => 'required',
            'amount' => 'required|numeric',
            'transactionFsount' => 'required|numeric',
            'systemFee' => 'required|numeric',
            'originName' => 'required',
        ];
        return $rule;
    }

    // Wallet Trading (decrease)
    public function plgCmdWalletDecreaseHandlerRule()
    {
        $rule = [
            'type' => 'required|in:4,5,6',
            'uid' => 'required',
            'amount' => 'required|numeric',
            'transactionFsount' => 'required|numeric',
            'systemFee' => 'required|numeric',
            'originName' => 'required',
        ];
        return $rule;
    }

    public function plgCmdUserRegisterHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2,3',
            'nickname' => 'required',
        ];
        return $rule;
    }

    public function plgCmdUserLoginHandlerRule()
    {
        $rule = [
            'type' => 'required|in:1,2',
            'account' => 'required',
        ];
        return $rule;
    }
}
