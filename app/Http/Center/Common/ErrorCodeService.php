<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Common;

class ErrorCodeService
{
    const CODE_OK = 0;

    // Extensions
    const PLUGINS_CONFIG_ERROR = 30000;
    const PLUGINS_CLASS_ERROR = 30001;
    const PLUGINS_TIMEOUT_ERROR = 30002;
    const PLUGINS_IS_ENABLE_ERROR = 30003;
    const PLUGINS_PARAM_ERROR = 30004;
    const PLUGINS_HANDLE_ERROR = 30005;
    const CODE_PARAM_ERROR = 30006;
    const DATA_EXCEPTION_ERROR = 30007;
    const HELPER_EXCEPTION_ERROR = 30008;
    const VERIFY_CODE_CHECK_ERROR = 30009;
    const PRIVATE_MODE_ERROR = 30010;
    const CALLBACK_ERROR = 30011;
    const CALLBACK_UUID_ERROR = 30012;
    const CALLBACK_TIME_ERROR = 30013;
    const CALLBACK_STATUS_ERROR = 30014;

    // Header
    const HEADER_ERROR = 30100;
    const HEADER_SIGN_ERROR = 30101;
    const HEADER_SIGN_EXPIRED = 30102;
    const HEADER_INFO_ERROR = 30103;
    const HEADER_PLATFORM_ERROR = 30104;
    const HEADER_APP_ID_ERROR = 30105;
    const HEADER_KEY_ERROR = 30106;
    const UID_REQUIRED_ERROR = 30107;
    const MID_REQUIRED_ERROR = 30108;
    const USER_CHECK_ERROR = 30109;
    const MEMBER_CHECK_ERROR = 30110;
    const USER_TOKEN_ERROR = 30111;
    const MEMBER_TOKEN_ERROR = 30112;
    const TOKEN_IS_ENABLE_ERROR = 30113;

    // User
    const REGISTER_EMAIL_ERROR = 30200;
    const REGISTER_PHONE_ERROR = 30201;
    const REGISTER_USER_ERROR = 30202;
    const PASSWORD_LENGTH_ERROR = 30203;
    const PASSWORD_NUMBER_ERROR = 30204;
    const PASSWORD_LOWERCASE_ERROR = 30205;
    const PASSWORD_CAPITAL_ERROR = 30206;
    const PASSWORD_SYMBOL_ERROR = 30207;

    const EMAIL_ERROR = 30208;
    const EMAIL_REGEX_ERROR = 30209;
    const EMAIL_EXIST_ERROR = 30210;
    const EMAIL_BAND_ERROR = 30211;
    const PHONE_ERROR = 30212;
    const PHONE_REGEX_ERROR = 30213;
    const PHONE_EXIST_ERROR = 30214;
    const PHONE_BAND_ERROR = 30215;
    const COUNTRY_CODE_ERROR = 30216;
    const CODE_TEMPLATE_ERROR = 30217;
    const CONNECT_TOKEN_ERROR = 30218;

    const ACCOUNT_IS_EMPTY_ERROR = 30219;
    const ACCOUNT_CHECK_ERROR = 30220;
    const ACCOUNT_PASSWORD_INVALID = 30221;
    const ACCOUNT_ERROR = 30222;
    const ACCOUNT_COUNT_ERROR = 30223;

    const USER_ERROR = 30224;
    const USER_IS_ENABLE_ERROR = 30225;
    const USER_WALLETS_ERROR = 30226;
    const USER_BALANCE_ERROR = 30227;
    const BALANCE_CLOSING_BALANCE_ERROR = 30228;
    const TO_USER_WALLETS_ERROR = 30229;
    const TO_BALANCE_CLOSING_BALANCE_ERROR = 30230;

    // Member
    const MEMBER_FAIL = 30300;
    const MEMBER_ERROR = 30301;
    const MEMBER_IS_ENABLE_ERROR = 30302;
    const MEMBER_PASSWORD_INVALID = 30303;
    const MEMBER_EXPIRED_ERROR = 30304;
    const MEMBER_NO_PERMISSION = 30305;
    const MEMBER_NAME_ERROR = 30306;
    const UPDATE_TIME_ERROR = 30307;
    const DISABLE_NAME_ERROR = 30308;

    // Member Mark
    const MARK_NOT_ENABLE = 30309;
    const MARK_FOLLOW_ERROR = 30310;
    const MARK_REPEAT_ERROR = 30311;

    // Member Role
    const ROLE_NO_CONFIG_ERROR = 30400;
    const ROLE_NO_PERMISSION = 30401;
    const ROLE_NO_PERMISSION_BROWSE = 30402;
    const ROLE_NO_PERMISSION_PUBLISH = 30403;
    const ROLE_PUBLISH_LIMIT = 30404;
    const ROLE_PUBLISH_EMAIL_VERIFY = 30405;
    const ROLE_PUBLISH_PHONE_VERIFY = 30406;
    const ROLE_PUBLISH_PROVE_VERIFY = 30407;
    const ROLE_NO_PERMISSION_UPLOAD_IMAGE = 30408;
    const ROLE_NO_PERMISSION_UPLOAD_VIDEO = 30409;
    const ROLE_NO_PERMISSION_UPLOAD_AUDIO = 30410;
    const ROLE_NO_PERMISSION_UPLOAD_DOC = 30411;
    const ROLE_UPLOAD_FILES_SIZE_ERROR = 30412;
    const ROLE_DIALOG_ERROR = 30413;
    const ROLE_DOWNLOAD_ERROR = 30414;

    // Dialog
    const DIALOG_ERROR = 30500;
    const DIALOG_MESSAGE_ERROR = 30501;
    const SEND_ME_ERROR = 30502;
    const FILE_OR_TEXT_ERROR = 30503;
    const DIALOG_LIMIT_2_ERROR = 30504;
    const DIALOG_LIMIT_3_ERROR = 30505;
    const DIALOG_WORD_ERROR = 30506;
    const DIALOG_OR_MESSAGE_ERROR = 30507;
    const DELETE_NOTIFY_ERROR = 30508;

    // Group Configs
    const GROUP_MARK_FOLLOW_ERROR = 30600;
    const GROUP_TYPE_ERROR = 30601;
    const GROUP_POST_ALLOW_ERROR = 30602;
    const GROUP_COMMENTS_ALLOW_ERROR = 30603;

    // Publish Configs
    const PUBLISH_EMAIL_VERIFY_ERROR = 30700;
    const PUBLISH_PHONE_VERIFY_ERROR = 30701;
    const PUBLISH_PROVE_VERIFY_ERROR = 30702;
    const PUBLISH_LIMIT_ERROR = 30703;
    const POSTS_EDIT_ERROR = 30704;
    const COMMENTS_EDIT_ERROR = 30705;
    const EDIT_STICKY_ERROR = 30706;
    const EDIT_TIME_ERROR = 30707;
    const EDIT_ESSENCE_ERROR = 30708;
    const UPLOAD_FILES_SUFFIX_ERROR = 30709;
    const POST_BROWSE_ERROR = 30710;

    // Main Content
    const GROUP_EXIST_ERROR = 30800;
    const HASHTAG_EXIST_ERROR = 30801;
    const POST_EXIST_ERROR = 30802;
    const COMMENT_EXIST_ERROR = 30803;
    const POST_LOG_EXIST_ERROR = 30804;
    const COMMENT_LOG_EXIST_ERROR = 30805;
    const POST_APPEND_ERROR = 30806;
    const COMMENT_APPEND_ERROR = 30807;
    const FILE_EXIST_ERROR = 30808;
    const EXTEND_EXIST_ERROR = 30809;
    const DELETE_CONTENT_ERROR = 30810;
    const DELETE_POST_ERROR = 30811;
    const DELETE_COMMENT_ERROR = 30812;
    const DELETE_FILE_ERROR = 30813;
    const DELETE_EXTEND_ERROR = 30814;

    // Editor
    const POST_STATE_2_ERROR = 30815;
    const POST_STATE_3_ERROR = 30816;
    const COMMENT_STATE_2_ERROR = 30817;
    const COMMENT_STATE_3_ERROR = 30818;
    const POST_SUBMIT_STATE_2_ERROR = 30819;
    const POST_SUBMIT_STATE_3_ERROR = 30820;
    const COMMENT_SUBMIT_STATE_2_ERROR = 30821;
    const COMMENT_SUBMIT_STATE_3_ERROR = 30822;
    const POST_REMOKE_ERROR = 30823;
    const COMMENT_REMOKE_ERROR = 30824;
    const CONTENT_AUTHOR_ERROR = 30825;
    const COMMENT_CREATE_ERROR = 30826;

    // Editor Check Parameters
    const MEMBER_LIST_JSON_ERROR = 30900;
    const COMMENT_SET_JSON_ERROR = 30901;
    const ALLOW_JSON_ERROR = 30902;
    const LOCATION_JSON_ERROR = 30903;
    const FILES_JSON_ERROR = 30904;
    const EXTENDS_JSON_ERROR = 30905;
    const EXTENDS_JSON_EID_ERROR = 30906;
    const FILE_INFO_JSON_ERROR = 30907;
    const COMMENT_PID_ERROR = 30908;
    const COMMENT_PID_EXIST_ERROR = 30909;
    const TITLE_ERROR = 30910;
    const CONTENT_STOP_WORDS_ERROR = 30911;
    const CONTENT_CHECK_PARAMS_ERROR = 30912;
    const CONTENT_TYPES_ERROR = 30913;
    const CONTENT_COUNT_ERROR = 30914;

    // Console Error Message
    const BACKEND_PATH_ERROR = 40000;
    const DELETE_ADMIN_ERROR = 40001;
    const KEY_NAME_ERROR = 40002;
    const KEY_PLATFORM_ERROR = 40003;
    const KEY_PLUGIN_ERROR = 40004;

    // Console Manage Extensions
    const UNINSTALL_EXTENSION_ERROR = 40100;
    const PLUGIN_UNIKEY_ERROR = 40101;
    const FOLDER_NAME_EMPTY_ERROR = 40102;
    const EXTENSION_DOWMLOAD_ERROR = 40103;

    private static $CODE_MSG_MAP = [
        self::CODE_OK                           => 'ok',

        // Extensions
        self::PLUGINS_CONFIG_ERROR              => '未配置服务商',
        self::PLUGINS_CLASS_ERROR               => '服务商不存在',
        self::PLUGINS_TIMEOUT_ERROR             => '服务商未响应',
        self::PLUGINS_IS_ENABLE_ERROR           => '服务商未启用',
        self::PLUGINS_PARAM_ERROR               => '服务商配置参数为空',
        self::PLUGINS_HANDLE_ERROR              => '服务商处理失败',
        self::CODE_PARAM_ERROR                  => '参数错误',
        self::DATA_EXCEPTION_ERROR              => '数据异常，查询不到或者数据重复',
        self::HELPER_EXCEPTION_ERROR            => '执行异常，文件丢失或者记录错误',
        self::VERIFY_CODE_CHECK_ERROR           => '验证码不正确或验证码已过期',
        self::PRIVATE_MODE_ERROR                => '私有模式禁止请求该接口',
        self::CALLBACK_ERROR                    => '回调异常',
        self::CALLBACK_UUID_ERROR               => 'UUID 错误或者记录不存在',
        self::CALLBACK_TIME_ERROR               => '记录已超时失效',
        self::CALLBACK_STATUS_ERROR             => '记录已被使用过，请重新操作',

        // Header
        self::HEADER_ERROR                      => 'Header 未知错误',
        self::HEADER_SIGN_ERROR                 => '签名错误',
        self::HEADER_SIGN_EXPIRED               => '签名已过期',
        self::HEADER_INFO_ERROR                 => '输入信息错误',
        self::HEADER_PLATFORM_ERROR             => '平台 ID 不存在',
        self::HEADER_APP_ID_ERROR               => 'App ID 不存在',
        self::HEADER_KEY_ERROR                  => '密钥无权请求本接口',
        self::UID_REQUIRED_ERROR                => 'UID 必传',
        self::MID_REQUIRED_ERROR                => 'MID 必传',
        self::USER_CHECK_ERROR                  => '用户错误或者不存在',
        self::MEMBER_CHECK_ERROR                => '成员错误或者不存在',
        self::USER_TOKEN_ERROR                  => '用户 Token 不正确',
        self::MEMBER_TOKEN_ERROR                => '成员 Token 不正确',
        self::TOKEN_IS_ENABLE_ERROR             => 'Token 未启用',

        // User
        self::REGISTER_EMAIL_ERROR              => '不支持邮箱注册',
        self::REGISTER_PHONE_ERROR              => '不支持手机号注册',
        self::REGISTER_USER_ERROR               => '该用户已注册',
        self::PASSWORD_LENGTH_ERROR             => '密码长度不正确',
        self::PASSWORD_NUMBER_ERROR             => '密码应包含数字',
        self::PASSWORD_LOWERCASE_ERROR          => '密码应包含小写字母',
        self::PASSWORD_CAPITAL_ERROR            => '密码应包含大写数字',
        self::PASSWORD_SYMBOL_ERROR             => '密码应包含符号',

        self::EMAIL_ERROR                       => '邮箱已被注册',
        self::EMAIL_REGEX_ERROR                 => '邮箱格式不正确',
        self::EMAIL_EXIST_ERROR                 => '邮箱不存在',
        self::EMAIL_BAND_ERROR                  => '已绑定邮箱',
        self::PHONE_ERROR                       => '手机号已被注册',
        self::PHONE_REGEX_ERROR                 => '手机号格式不正确',
        self::PHONE_EXIST_ERROR                 => '手机号不存在',
        self::PHONE_BAND_ERROR                  => '已绑定手机',
        self::COUNTRY_CODE_ERROR                => '国际区号错误',
        self::CODE_TEMPLATE_ERROR               => '验证码模板关闭或者不存在',
        self::CONNECT_TOKEN_ERROR               => '互联 Token 已存在',

        self::ACCOUNT_IS_EMPTY_ERROR            => '账号不能为空',
        self::ACCOUNT_CHECK_ERROR               => '账号错误或者不存在',
        self::ACCOUNT_PASSWORD_INVALID          => '账号密码不正确',
        self::ACCOUNT_ERROR                     => '账号不正确或者密码错误',
        self::ACCOUNT_COUNT_ERROR               => '错误已超系统限制，请 1 小时后再登录',

        self::USER_ERROR                        => '该用户已注销',
        self::USER_IS_ENABLE_ERROR              => '当前用户已被禁用',
        self::USER_WALLETS_ERROR                => '用户钱包不存在',
        self::USER_BALANCE_ERROR                => '钱包余额不允许支付',
        self::BALANCE_CLOSING_BALANCE_ERROR     => '期末余额和钱包额度不匹配',
        self::TO_USER_WALLETS_ERROR             => '对方钱包不存在',
        self::TO_BALANCE_CLOSING_BALANCE_ERROR  => '对方期末余额和钱包额度不匹配',

        // Member
        self::MEMBER_FAIL                       => '当前成员不存在或者不属于当前用户',
        self::MEMBER_ERROR                      => '该成员已注销',
        self::MEMBER_IS_ENABLE_ERROR            => '当前成员已被禁用',
        self::MEMBER_PASSWORD_INVALID           => '密码不正确',
        self::MEMBER_EXPIRED_ERROR              => '成员已过期，无权操作该功能',
        self::MEMBER_NO_PERMISSION              => '当前成员无权请求',
        self::MEMBER_NAME_ERROR                 => '成员名称不允许重复',
        self::UPDATE_TIME_ERROR                 => '指定天数内只能修改一次',
        self::DISABLE_NAME_ERROR                => '名称存在禁用词',

        // Member Mark
        self::MARK_NOT_ENABLE                   => '未开启该项操作功能',
        self::MARK_FOLLOW_ERROR                 => '不能对自己操作',
        self::MARK_REPEAT_ERROR                 => '不允许重复操作',

        // Member Role
        self::ROLE_NO_CONFIG_ERROR              => '当前角色未配置权限，请联系管理员确认',
        self::ROLE_NO_PERMISSION                => '当前角色无权请求',
        self::ROLE_NO_PERMISSION_BROWSE         => '当前角色无权浏览',
        self::ROLE_NO_PERMISSION_PUBLISH        => '当前角色无权发表',
        self::ROLE_PUBLISH_LIMIT                => '当前角色发表内容有时间限制，请在规定的时间内再发表',
        self::ROLE_PUBLISH_EMAIL_VERIFY         => '当前角色发表内容需要先绑定邮箱',
        self::ROLE_PUBLISH_PHONE_VERIFY         => '当前角色发表内容需要先绑定手机号',
        self::ROLE_PUBLISH_PROVE_VERIFY         => '当前角色发表内容需要先实名制认证',
        self::ROLE_NO_PERMISSION_UPLOAD_IMAGE   => '当前角色无权上传图片',
        self::ROLE_NO_PERMISSION_UPLOAD_VIDEO   => '当前角色无权上传视频',
        self::ROLE_NO_PERMISSION_UPLOAD_AUDIO   => '当前角色无权上传音频',
        self::ROLE_NO_PERMISSION_UPLOAD_DOC     => '当前角色无权上传文档',
        self::ROLE_UPLOAD_FILES_SIZE_ERROR      => '文件大小超过当前角色限制',
        self::ROLE_DIALOG_ERROR                 => '当前角色无私信权限',
        self::ROLE_DOWNLOAD_ERROR               => '当前角色已经达到今天下载次数上限，请明天再下载',

        // Dialog
        self::DIALOG_ERROR                      => '会话异常或者该会话不属于当前成员',
        self::DIALOG_MESSAGE_ERROR              => '消息已删除',
        self::SEND_ME_ERROR                     => '自己不能给自己发送信息',
        self::FILE_OR_TEXT_ERROR                => '单个消息只能「文件」或「文本」二选一',
        self::DIALOG_LIMIT_2_ERROR              => '对方已设置仅允许 TA 关注的成员才能给 TA 发送消息',
        self::DIALOG_LIMIT_3_ERROR              => '对方已设置仅允许 TA 关注的成员和已认证的成员才能给 TA 发送消息',
        self::DIALOG_WORD_ERROR                 => '消息文本中含有禁用词，不能发送',
        self::DIALOG_OR_MESSAGE_ERROR           => '会话和消息只能传其中一个，不能同时删除两种类型',
        self::DELETE_NOTIFY_ERROR               => '只能删除自己的消息',

        // Group Configs
        self::GROUP_MARK_FOLLOW_ERROR           => '仅支持指定方式操作，本接口禁止操作',
        self::GROUP_TYPE_ERROR                  => '小组分类下不允许发表',
        self::GROUP_POST_ALLOW_ERROR            => '当前成员无该小组的发帖权限',
        self::GROUP_COMMENTS_ALLOW_ERROR        => '当前成员无该小组的评论权限',

        // Publish Configs
        self::PUBLISH_EMAIL_VERIFY_ERROR        => '发表内容需要先绑定邮箱',
        self::PUBLISH_PHONE_VERIFY_ERROR        => '发表内容需要先绑定手机号',
        self::PUBLISH_PROVE_VERIFY_ERROR        => '发表内容需要先实名制认证',
        self::PUBLISH_LIMIT_ERROR               => '系统已经开启发表时间限制，请在规定的时间内再发表',
        self::POSTS_EDIT_ERROR                  => '不允许编辑帖子',
        self::COMMENTS_EDIT_ERROR               => '不允许编辑评论',
        self::EDIT_STICKY_ERROR                 => '置顶后不允许编辑',
        self::EDIT_TIME_ERROR                   => '超出可编辑时间',
        self::EDIT_ESSENCE_ERROR                => '加精后不允许编辑',
        self::UPLOAD_FILES_SUFFIX_ERROR         => '该文件类型不在允许上传的范围内',
        self::POST_BROWSE_ERROR                 => '该内容需要授权后才能浏览',

        // Main Content
        self::GROUP_EXIST_ERROR                 => '小组错误或者不存在',
        self::HASHTAG_EXIST_ERROR               => '话题错误或者不存在',
        self::POST_EXIST_ERROR                  => '帖子错误或者不存在',
        self::COMMENT_EXIST_ERROR               => '评论错误或者不存在',
        self::POST_LOG_EXIST_ERROR              => '帖子草稿错误或者不存在',
        self::COMMENT_LOG_EXIST_ERROR           => '评论草稿错误或者不存在',
        self::POST_APPEND_ERROR                 => '帖子异常，未找到帖子副表记录',
        self::COMMENT_APPEND_ERROR              => '评论异常，未找到评论副表记录',
        self::FILE_EXIST_ERROR                  => '文件错误或者不存在',
        self::EXTEND_EXIST_ERROR                => '扩展错误或者不存在',
        self::DELETE_CONTENT_ERROR              => '该内容不允许删除',
        self::DELETE_POST_ERROR                 => '删除失败，帖子错误或者不存在',
        self::DELETE_COMMENT_ERROR              => '删除失败，评论错误或者不存在',
        self::DELETE_FILE_ERROR                 => '该文件正在被使用，不允许删除',
        self::DELETE_EXTEND_ERROR               => '该扩展内容有其他人使用，不允许删除',

        // Editor
        self::POST_STATE_2_ERROR                => '帖子审核中不可编辑',
        self::POST_STATE_3_ERROR                => '帖子已正式发表不可编辑',
        self::COMMENT_STATE_2_ERROR             => '评论审核中不可编辑',
        self::COMMENT_STATE_3_ERROR             => '评论已正式发表不可编辑',
        self::POST_SUBMIT_STATE_2_ERROR         => '处于审核状态的帖子不可再提交',
        self::POST_SUBMIT_STATE_3_ERROR         => '处于发布状态的帖子不可再提交',
        self::COMMENT_SUBMIT_STATE_2_ERROR      => '处于审核状态的评论不可再提交',
        self::COMMENT_SUBMIT_STATE_3_ERROR      => '处于发布状态的评论不可再提交',
        self::POST_REMOKE_ERROR                 => '当前帖子并非审核状态，无需撤回',
        self::COMMENT_REMOKE_ERROR              => '当前评论并非审核状态，无需撤回',
        self::CONTENT_AUTHOR_ERROR              => '操作失败，请确认是作者本人',
        self::COMMENT_CREATE_ERROR              => '评论草稿创建失败，只有一级评论才能创建草稿',

        // Editor Check Parameters
        self::MEMBER_LIST_JSON_ERROR            => 'memberListJson 格式错误或者数据异常',
        self::COMMENT_SET_JSON_ERROR            => 'commentSetJson 格式错误或者数据异常',
        self::ALLOW_JSON_ERROR                  => 'allowJson 格式错误或者数据异常',
        self::LOCATION_JSON_ERROR               => 'locationJson 格式错误或者数据异常',
        self::FILES_JSON_ERROR                  => 'filesJson 格式错误或者数据异常',
        self::EXTENDS_JSON_ERROR                => 'extendsJson 格式错误或者数据异常',
        self::EXTENDS_JSON_EID_ERROR            => 'extendsJson 中 eid 参数必填',
        self::FILE_INFO_JSON_ERROR              => 'fileInfo 格式错误或者数据异常',
        self::COMMENT_PID_ERROR                 => '发表评论，必传 PID 参数',
        self::COMMENT_PID_EXIST_ERROR           => '评论失败，未找到帖子信息',
        self::TITLE_ERROR                       => '标题过长，应小于 255 字符',
        self::CONTENT_STOP_WORDS_ERROR          => '内容存在禁用词，请修改后再发表',
        self::CONTENT_CHECK_PARAMS_ERROR        => '内容、文件、扩展内容，三种不可全部为空，至少其中一个有值',
        self::CONTENT_TYPES_ERROR               => '内容类型参数错误或者字符数达到上限',
        self::CONTENT_COUNT_ERROR               => '内容超过限制字数',

        // Console Error Message
        self::BACKEND_PATH_ERROR                => '该入口命名已被占用',
        self::DELETE_ADMIN_ERROR                => '不允许删除自己',
        self::KEY_NAME_ERROR                    => '密钥名称必填',
        self::KEY_PLATFORM_ERROR                => '请选择密钥应用平台',
        self::KEY_PLUGIN_ERROR                  => '请选择关联插件',

        // Console Manage Extensions
        self::PLUGIN_UNIKEY_ERROR               => 'UniKey 错误',
        self::UNINSTALL_EXTENSION_ERROR         => '停用后才能卸载',
        self::FOLDER_NAME_EMPTY_ERROR           => '文件夹名不能为空',
        self::EXTENSION_DOWMLOAD_ERROR          => '扩展安装包下载失败',
    ];

    // Get Message
    public static function getMsg($code, $data = [])
    {
        if (! isset(self::$CODE_MSG_MAP[$code])) {
            return 'Plugin Check Exception';
        }

        // Specifying information about parameter errors
        try {
            if ($code == self::CODE_PARAM_ERROR) {
                $data = (array) $data;
                foreach ($data as $key => $messageBag) {
                    foreach ($messageBag as $k => $infoArr) {
                        if (count($infoArr) > 0) {
                            return $infoArr[0];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            LogService::warning('get error msg missing ', $data);
        }

        return self::$CODE_MSG_MAP[$code];
    }
}
