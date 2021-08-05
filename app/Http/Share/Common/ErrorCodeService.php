<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Share\Common;

class ErrorCodeService
{
    CONST CODE_OK = 0;
    CONST HEADER_ERROR = 30000;
    CONST CODE_FAIL = 30001;
    CONST PASSWORD_INVALID = 30002;
    CONST UPDATE_TIME_ERROR = 30003;
    CONST WXAPP_CONTENT_ERROR = 30004;
    CONST FOLLOW_ERROR = 30005;
    CONST DELETE_FILE_ERROR = 30006;
    CONST NO_PERMISSION = 30007;
    CONST DELETE_COMMENT_ERROR = 30008;
    CONST SMS_CODE_CHECK_ERROR = 30009;
    CONST CODE_PHONE_ERROR = 30010;
    CONST USERS_NOT_AUTHORITY_ERROR  = 30011;
    CONST CODE_EXCEPTION = 30012;
    CONST NO_RECORD = 30013;
    CONST COMMENT_LOGS_ERROR = 30014;
    CONST POSTS_LOGS_EXISTS_ERROR = 30015;
    CONST COMMENT_LOGS_EXISTS_ERROR = 30016;
    CONST POSTS_LOGS_STATUS_ERROR = 30017;
    CONST COMMENTS_LOGS_STATUS_ERROR = 30018;
    CONST CODE_PARAM_ERROR = 30019;
    CONST FILE_SALE_ERROR = 30020;
    CONST POSTS_USER_ERROR = 30021;
    CONST COMMENTS_USER_ERROR = 30022;
    CONST DELETED_ERROR = 30023;
    CONST CODE_SIGN_ERROR = 30024;
    CONST USER_REQUIRED_ERROR = 30025;
    CONST MEMBER_REQUIRED_ERROR = 30026;
    CONST DELETED_NOTIFY_ERROR = 30027;
    CONST DIALOG_ERROR = 30028;
    CONST DIALOG_OR_MESSAGE_ERROR = 30029;
    // CONST MEMBER_ROLE_ERROR = 30030;
    CONST FILE_OR_MESSAGE_ERROR = 30031;
    CONST MEMBER_ERROR = 30032;
    CONST MEMBER_FOLLOW_ERROR = 30033;
    CONST VERIFIED_ERROR = 30034;
    CONST FILES_ERROR = 30035;
    CONST DIALOG_WORD_ERROR = 30036;
    CONST POST_REMOKE_ERROR = 30037;
    CONST COMMENT_REMOKE_ERROR = 30038;
    CONST REGISTER_EMAIL_ERROR = 30039;
    CONST REGISTER_PHONE_ERROR = 30040;
    CONST REGISTER_USER_ERROR = 30041;
    CONST PASSWORD_LENGTH_ERROR = 30042;
    CONST PASSWORD_NUMBER_ERROR = 30043;
    CONST PASSWORD_LOWERCASE_ERROR = 30044;
    CONST PASSWORD_CAPITAL_ERROR = 30045;
    CONST PASSWORD_SYMBOL_ERROR = 30046;
    CONST USER_TOKEN_ERROR = 30047;
    CONST USER_EXPIRED_ERROR = 30048;
    CONST USER_BINDING_EMAIL_ERROR = 30049;
    CONST USER_BINDING_PHONE_ERROR = 30050;
    CONST USER_BINDING_REAL_NAME_ERROR = 30051;
    CONST POSTS_SUBMIT_ERROR = 30052;
    CONST POSTS_UPDATE_ERROR = 30053;
    CONST COMMENTS_SUBMIT_ERROR = 30054;
    CONST COMMENTS_UPDATE_ERROR = 30055;
    CONST UID_EXIST_ERROR = 30056;
    CONST MEMBER_ROLE_ERROR = 30083;
    CONST MEMBER_ME_ERROR = 30089;
    CONST MEMBER_EXPIRED_ERROR = 30091;
    CONST HEADER_EXSIT_MEMBER = 30093;

    CONST HEADER_IS_ENABLE_ERROR = 30094;
    CONST HEADER_TYPE_ERROR = 30095;
    CONST MEMBER_NAME_ERROR = 30096;
    CONST GROUP_MARK_FOLLOW_TYPE_ERROR = 30097;
    CONST API_NO_CALL_ERROR = 30098;
    CONST UPLOAD_FILES_SIZE_ERROR = 30099;
    CONST DIALOGS_MESSAGE_ERROR = 30100;

    CONST PERMISSION_NO_SETTING_ERROR = 30101;
    CONST SUBMIT_NO_ERROR = 30102;
    CONST SUBMIT_LIMIT_ERROR = 30103;
    CONST EDIT_TOP_ERROR = 30104;
    CONST EDIT_TIME_ERROR = 30105;
    CONST EDIT_ESSENCE_ERROR = 30106;

    CONST MEMBER_ROLE_SUBMIT_NO_ERROR = 30107;
    CONST MEMBER_ROLE_SUBMIT_LIMIT_ERROR = 30108;
    CONST MEMBER_ROLE_USER_BINDING_EMAIL_ERROR = 30109;
    CONST MEMBER_ROLE_USER_BINDING_PHONE_ERROR = 30110;
    CONST MEMBER_ROLE_USER_BINDING_REAL_NAME_ERROR = 30111;
    CONST POST_MEMBER_ERROR = 30112;
    CONST PLUGIN_ERROR = 30113;
    CONST MEMBER_JSON_ERROR = 30114;
    CONST ALLOW_JSON_ERROR = 30115;
    CONST LOCATION_JSON_ERROR = 30116;
    CONST FILE_JSON_ERROR = 30117;
    CONST EXTENDS_JSON_ERROR = 30118;
    CONST COMMENT_JSON_ERROR = 30119;
    CONST MEMBER_MARK_ERROR = 30120;
    CONST POST_APPEND_ERROR = 30121;
    CONST COMMENT_APPEND_ERROR = 30122;
    CONST EXTEND_ERROR = 30123;
    CONST MEMBER_EXPIRED_AT_ERROR = 30124;
    CONST MEMBER_UPLOAD_FILES_SIZE_ERROR = 30125;
    CONST UPLOAD_FILES_SUFFIX_ERROR = 30126;

    CONST DELETE_FILES_ERROR = 30135;
    CONST DELETE_EXTEND_ERROR = 30136;
    CONST FILES_INFO_ERROR = 30137;


    // CONST HEADER_ERROR_ = 30096;
    // CONST HEADER_ERROR_UID = 30097;
    // CONST HEADER_ERROR_UID = 30098;




    // 面板错误码
    CONST CODE_LOGIN_ERROR = 30200;
    // CONST CODE_FILE_UPLOAD_ERROR = 30201;
    // CONST PARAMS_IDS_INVALID = 30202;
    // CONST PERMISSION_NOT_EXIST = 30203;
    // CONST NOT_IMPLEMENTS  = 30204;
    // CONST CATEGORY_IDS_INVALID = 30205;
    // CONST TAG_IDS_INVALID = 30206;
    // CONST TOPIC_IDS_INVALID = 30207;
    // CONST CODE_PHONE_LOGIN_ERROR = 30209;
    // CONST CODE_REGISTER_PASSWORD_ERROR = 30210;
    // CONST CODE_EMAIL_LOGIN_ERROR = 30211;
    // CONST CODE_IS_EMAIL = 30212;
    // CONST CODE_SMS_ERROR = 30213;
    // // CONST PASSWORD_INVALID = 30021;
    // CONST ACCOUNT_NAME = 30214;
    // CONST ACCOUNT_LOGIN_NAME = 30215;
    // CONST CODE_PHONE_UPDATE = 30216;
    // CONST CODE_LOG_IN_ERROR = 30217;
    // CONST CODE_SEND_SMS_ERROR = 30218;
    // CONST FORGET_NAME_ERROR = 30219;
    // CONST CODE_SEND_SMS_TIME_ERROR = 30220;
    // CONST CODE_PHONE_CODE_ERROR = 30221;
    // CONST CODE_EMAIL_CODE_ERROR = 30222;
    // CONST CODE_LOGIN_ERROR_USER = 30223;
    // CONST TABLE_NOT_EXIST = 30224;

    // CONST EMAIL_LOGIN_ERROR = 31118;
    // CONST EMAIL_ACTIVE_ERROR = 30229;

    CONST DOWMLOAD_ERROR = 30201;
    CONST FILES_JSON_ERROR = 30202;
    CONST PLUGIN_ENABLE_ERROR = 30203;
    CONST PLUGIN_UNIKEY_ERROR = 30204;
    CONST LOGIN_ERROR = 30205;
    CONST ACCOUNT_ERROR = 30206;
    CONST ADMIN_ACCOUNT_ERROR = 30207;
    CONST FILES_EMPTY_ERROR = 30208;
    CONST FILES_AUTH_ERROR = 30209;
    CONST CODE_CAPTCHA_ERROR = 30210;
    CONST BACKEND_PATH_ERROR = 30211;
    CONST KEYS_NAME_ERROR = 30212;
    CONST KEYS_PLAT_ERROR = 30213;
    CONST PLUGIN_PLAT_ERROR = 30214;
    CONST DELETE_PACK_ERROR = 30215;
    CONST REPEAT_PARTNER_ERROR = 30216;



    // 插件错误码
    CONST CONFIGS_SERVER_ERROR = 30300;
    CONST PLUGINS_CLASS_ERROR = 30301;
    CONST CAPTCHA_ERROR = 30302;
    CONST PLUGINS_TIMEOUT_ERROR = 30303;


    private static $CODE_MSG_MAP = [
        self::CODE_OK                         => 'ok',
        self::HEADER_ERROR                    => 'header error',
        self::CODE_FAIL                       => '请确认该成员在当前用户下存在',
        self::PASSWORD_INVALID                => '密码不正确',
        self::UPDATE_TIME_ERROR               => '未过修改时间间隔',
        self::WXAPP_CONTENT_ERROR             => '内容违规',
        self::FOLLOW_ERROR                    => '自己不能操作标记自己',
        self::DELETE_FILE_ERROR               => '帖子不存在',
        self::NO_PERMISSION                   => 'no permission',
        self::DELETE_COMMENT_ERROR            => '评论不存在',
        self::SMS_CODE_CHECK_ERROR            => 'Verification code error',
        self::CODE_PHONE_ERROR                => '该用户未注册',
        self::USERS_NOT_AUTHORITY_ERROR       => '当前用户无权操作',
        self::CODE_EXCEPTION                  => 'exception',
        self::NO_RECORD                       => '记录不存在',
        self::COMMENT_LOGS_ERROR              => '只有一级评论可以有草稿',
        self::POSTS_LOGS_EXISTS_ERROR         => '帖子草稿不存在',
        self::COMMENT_LOGS_EXISTS_ERROR       => '评论草稿不存在',
        self::POSTS_LOGS_STATUS_ERROR         => '帖子草稿不处于审核状态',
        self::COMMENTS_LOGS_STATUS_ERROR      => '评论草稿不处于审核状态',
        self::CODE_PARAM_ERROR                => '参数错误',
        self::FILE_SALE_ERROR                 => '未配置存储设置，请配置后再上传',
        self::POSTS_USER_ERROR                => '请确认该帖子是当前用户所写',
        self::COMMENTS_USER_ERROR             => '请确认该评论是当前用户所写',
        self::DELETED_ERROR                   => '已发布不允许删除',
        self::CODE_SIGN_ERROR                => '签名错误',
        self::USER_REQUIRED_ERROR            => 'uid required',
        self::MEMBER_REQUIRED_ERROR          => 'mid required',
        self::DELETED_NOTIFY_ERROR          => '只能删除自己的消息',
        self::DIALOG_ERROR                  => '非自己的会话',
        self::DIALOG_OR_MESSAGE_ERROR       => '会话和消息只能传其一',
        self::MEMBER_ROLE_ERROR             => '该成员无发送消息权限',
        self::MEMBER_ME_ERROR            => '自己不能给自己发送信息',
        self::FILE_OR_MESSAGE_ERROR             => '文件和消息只能传其一',
        self::MEMBER_ERROR             => '对方已注销',
        self::MEMBER_FOLLOW_ERROR             => '需关注对方才能发送消息',
        self::VERIFIED_ERROR             => '需认证才能给对方发消息',
        self::MEMBER_EXPIRED_ERROR             => '成员已过期，不能发送私信',
        self::FILES_ERROR             => '文件不存在',
        self::DIALOG_WORD_ERROR             => '存在屏蔽字，禁止发送',
        self::POST_REMOKE_ERROR             => '审核中的帖子才能撤回',
        self::COMMENT_REMOKE_ERROR             => '审核中的评论才能撤回',
        self::REGISTER_EMAIL_ERROR             => '不支持邮箱方式',
        self::REGISTER_PHONE_ERROR             => '不支持手机号方式',
        self::REGISTER_USER_ERROR             => '该用户已注册',
        self::PASSWORD_LENGTH_ERROR             => '密码长度不正确',
        self::PASSWORD_NUMBER_ERROR             => '密码应包含数字',
        self::PASSWORD_LOWERCASE_ERROR             => '密码应包含小写字母',
        self::PASSWORD_CAPITAL_ERROR             => '密码应包含大写数字',
        self::PASSWORD_SYMBOL_ERROR             => '密码应包含符号',
        self::USER_TOKEN_ERROR             => 'token不正确',
        self::USER_EXPIRED_ERROR             => '成员已过期',
        self::USER_BINDING_EMAIL_ERROR             => '请绑定邮箱',
        self::USER_BINDING_PHONE_ERROR             => '请绑定手机号',
        self::USER_BINDING_REAL_NAME_ERROR             => '请实名制',
        self::POSTS_SUBMIT_ERROR             => '不允许发布帖子',
        self::POSTS_UPDATE_ERROR             => '不允许编辑帖子',
        self::COMMENTS_SUBMIT_ERROR             => '不允许发布评论',
        self::COMMENTS_UPDATE_ERROR             => '不允许编辑评论',
        self::UID_EXIST_ERROR             => '用户不存在',
        self::HEADER_EXSIT_MEMBER        => '成员不存在',
        self::HEADER_IS_ENABLE_ERROR        => '未启用',
        self::HEADER_TYPE_ERROR        => '输入类型错误',
        self::MEMBER_NAME_ERROR        => '成员名称不允许重复',
        self::GROUP_MARK_FOLLOW_TYPE_ERROR        => '插件方式不允许操作',
        self::API_NO_CALL_ERROR        => '接口不允许调用',
        self::UPLOAD_FILES_SIZE_ERROR        => '文件超过上传大小',
        self::DIALOGS_MESSAGE_ERROR        => '消息已删除',

        self::PERMISSION_NO_SETTING_ERROR        => '未设置权限',
        self::SUBMIT_NO_ERROR        => '未开启发布权限',
        self::SUBMIT_LIMIT_ERROR        => '未在指定时间内不允许发布',
        self::EDIT_TOP_ERROR        => '置顶后不允许编辑',
        self::EDIT_TIME_ERROR        => '超出编辑时间',
        self::EDIT_ESSENCE_ERROR        => '加精不允许编辑',
        self::MEMBER_ROLE_SUBMIT_NO_ERROR        => '角色未开启发布权限',
        self::MEMBER_ROLE_SUBMIT_LIMIT_ERROR        => '角色权限未在指定时间内不允许发布',
        self::MEMBER_ROLE_USER_BINDING_EMAIL_ERROR        => '角色开启邮箱校验',
        self::MEMBER_ROLE_USER_BINDING_PHONE_ERROR        => '角色开启手机号校验',
        self::MEMBER_ROLE_USER_BINDING_REAL_NAME_ERROR        => '角色开启实名制校验',
        self::POST_MEMBER_ERROR        => '成员不一致',
        self::MEMBER_JSON_ERROR        => '请输入正确的memberJson格式',
        self::ALLOW_JSON_ERROR        => '请输入正确的allowJson格式',
        self::LOCATION_JSON_ERROR        => '请输入正确的locationJson格式',
        self::FILE_JSON_ERROR        => '请输入正确的fileJson格式',
        self::EXTENDS_JSON_ERROR        => '请输入正确的extendsJson格式',
        self::COMMENT_JSON_ERROR        => '请输入正确的commentSetJson格式',
        self::MEMBER_MARK_ERROR        => '不允许重复操作',
        self::COMMENT_APPEND_ERROR        => '评论异常，未找到评论副表记录',
        self::POST_APPEND_ERROR        => '帖子异常，未找到帖子副表记录',
        self::EXTEND_ERROR        => '扩展不存在',
        self::MEMBER_EXPIRED_AT_ERROR        => '私有模式，成员过期，不可调用',
        self::MEMBER_UPLOAD_FILES_SIZE_ERROR        => '角色权限文件超过上传大小',
        self::UPLOAD_FILES_SUFFIX_ERROR        => '文件后缀错误',
        self::LOGIN_ERROR        => '登录错误达到次数限制',
        self::DELETE_FILES_ERROR        => 'files在使用',
        self::DELETE_EXTEND_ERROR        => 'extend在使用',
        self::FILES_INFO_ERROR        => '请输入正确的fileInfo格式',


        self::CODE_LOGIN_ERROR                => '用户名或密码错误',
        // self::CODE_FILE_UPLOAD_ERROR          => 'file upload error',
        // self::PARAMS_IDS_INVALID              => 'id(s) params invalid',
        // self::PERMISSION_NOT_EXIST            => 'permission not exist',
        // self::NOT_IMPLEMENTS                  => 'not implements',
        // self::CATEGORY_IDS_INVALID            => 'category id(s) invalid',
        // self::TAG_IDS_INVALID                 => 'tag id(s) invalid',
        // self::TOPIC_IDS_INVALID               => 'topic id(s) invalid',
        self::CODE_CAPTCHA_ERROR              => '图片验证码错误',
        // self::CODE_PHONE_LOGIN_ERROR          => '该手机号已被注册',
        // self::CODE_REGISTER_PASSWORD_ERROR    => '两次输入密码不一致',
        // self::CODE_EMAIL_LOGIN_ERROR          => '该邮箱已被注册',
        // self::CODE_IS_EMAIL                   => '邮箱格式不对',
        // self::CODE_SMS_ERROR                  => '短信验证码错误',
        // self::ACCOUNT_NAME                    => '该昵称已被注册',
        // self::CODE_PHONE_UPDATE               => '请输入原手机号',
        // self::CODE_LOG_IN_ERROR               => '请先登录',
        // self::CODE_SEND_SMS_ERROR             => '短信发送错误',
        // self::FORGET_NAME_ERROR               => '用户名或邮箱错误',
        // self::CODE_SEND_SMS_TIME_ERROR        => '验证码已过期，请重新获取',
        // self::ACCOUNT_LOGIN_NAME              => '该用户名已被注册',
        // self::TABLE_NOT_EXIST                 => '数据表不存在',
        // self::EMAIL_ACTIVE_ERROR => '请先去邮箱激活',
        self::ACCOUNT_ERROR        => '账号必填',
        self::ADMIN_ACCOUNT_ERROR        => '邮箱或手机号找不到',
        self::BACKEND_PATH_ERROR        => '入口已被占用',
        self::KEYS_NAME_ERROR        => '密钥名称必填',
        self::KEYS_PLAT_ERROR        => '请选择密钥应用平台',
        self::PLUGIN_PLAT_ERROR        => '请选择插件',
        self::DELETE_PACK_ERROR        => '有部分删除无权限',
        self::REPEAT_PARTNER_ERROR        => '无需重复添加',


        self::DOWMLOAD_ERROR => '请下载或安装启用插件',
        self::FILES_JSON_ERROR => '插件目录下文件缺失',
        self::PLUGIN_ENABLE_ERROR => '插件停用后才能卸载',
        self::PLUGIN_UNIKEY_ERROR => '插件unikey错误',
        self::FILES_EMPTY_ERROR => '请输入文件名',
        self::FILES_AUTH_ERROR => '未安装权限',
        
        self::CONFIGS_SERVER_ERROR => '未配置发信服务商',
        self::PLUGINS_CLASS_ERROR => '未找到插件类',
        self::PLUGINS_TIMEOUT_ERROR => '插件未响应',
        self::CAPTCHA_ERROR => '验证码不正确或验证码已过期',
    ];


    public static function getMsg($code, $data = []){
        if(!isset(self::$CODE_MSG_MAP[$code])){
            return '插件检查异常';
        }

        // 关于参数错误的信息具体化

        try{
            if($code == self::CODE_PARAM_ERROR){
                $data = (array) $data;
                foreach ($data as $key => $messageBag){
                    foreach ($messageBag as $k => $infoArr){
                        if(count($infoArr) > 0){
                            return $infoArr[0];
                        }
                    }
                }
            }
        }catch(\Exception $e){
            LogService::warning("get error msg missing ", $data);
        }

        return self::$CODE_MSG_MAP[$code];
    }

}
