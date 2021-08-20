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
    CONST GROUP_EXISTS = 30057;
    CONST GROUP_TYPE_ERROR = 30058;
    CONST TITLE_ERROR = 30059;
    CONST POST_STATUS_2_ERROR = 30060;
    CONST POST_STATUS_3_ERROR = 30061;
    CONST COMMENT_STATUS_2_ERROR = 30062;
    CONST COMMENT_STATUS_3_ERROR = 30063;
    CONST POST_SUBMIT_STATUS2_ERROR = 30064;
    CONST POST_SUBMIT_STATUS3_ERROR = 30065;
    CONST COMMENT_SUBMIT_STATUS2_ERROR = 30066;
    CONST COMMENT_SUBMIT_STATUS3_ERROR = 30067;
    CONST POST_CONTENT_WORDS_ERROR = 30068;
    CONST COMMENT_CONTENT_WORDS_ERROR = 30069;
    CONST MEMBER_EXPIRED_LOGS_ERROR = 30070;
    CONST COMMENT_PID_ERROR = 30071;
    CONST COMMENT_PARENT_ERROR = 30072;
    CONST TYPE_ERROR = 30073;
    CONST EMAIL_ERROR = 30074;
    CONST EMAIL_REGEX_ERROR = 30075;
    CONST PHONE_REGEX_ERROR = 30076;
    CONST PHONE_ERROR = 30077;
    CONST EMAIL_EXIST_ERROR = 30078;
    CONST PHONE_EXIST_ERROR = 30079;
    CONST EMAIL_BAND_ERROR = 30080;
    CONST PHONE_BAND_ERROR = 30081;
    CONST COUNTRY_CODE_ERROR = 30082;
    CONST MEMBER_ROLE_ERROR = 30083;
    CONST TEAMPLAPE_ERROR = 30087;
    CONST MEMBER_ME_ERROR = 30089;
    CONST MEMBER_EXPIRED_ERROR = 30091;
    CONST FILE_OR_FILEINFO_ERROR = 30092;
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
    CONST POST_GROUP_ALLOW_ERROR = 30127;
    CONST POST_COMMENTS_POSTS_ERROR = 30128;
    CONST POST_COMMENTS_ALLOW_ERROR = 30129;
    CONST COMMENTS_LOG_EXIST_ERROR = 30130;
    CONST POSTS_LOG_EXIST_ERROR = 30131;
    CONST POSTS_LOG_CHECK_PARAMS_ERROR = 30132;
    CONST EXTENDS_UUID_ERROR = 30133;
    CONST CONTENT_COUNT_ERROR = 30134;

    CONST DELETE_FILES_ERROR = 30135;
    CONST DELETE_EXTEND_ERROR = 30136;
    CONST FILES_INFO_ERROR = 30137;
    CONST EXTENDS_EID_ERROR = 30138;
    CONST USER_WALLETS_ERROR = 30139;
    CONST BALANCE_CLOSING_BALANCE_ERROR = 30140;
    CONST USER_BALANCE_ERROR = 30141;
    CONST SING_EXPIRED_ERROR = 30142;
    CONST TO_USER_WALLETS_ERROR = 30143;
    CONST TO_BALANCE_CLOSING_BALANCE_ERROR = 30144;
    


    // 面板错误码
    CONST CODE_LOGIN_ERROR = 30200;
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
    CONST DELETE_ADMIN = 30217;
    CONST LANGUAGE_SETTING_ERROR = 30218;



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
        // self::GROUP_EXISTS       => '小组不存在',
        self::GROUP_TYPE_ERROR       => '小组分类不可发帖',
        self::TITLE_ERROR       => '标题过长(应小于255)',
        self::POST_STATUS_2_ERROR       => '帖子审核中不可编辑',
        self::POST_STATUS_3_ERROR       => '帖子已正式发表不可编辑',
        self::COMMENT_STATUS_2_ERROR       => '评论审核中不可编辑',
        self::COMMENT_STATUS_3_ERROR       => '评论已正式发表不可编辑',
        self::POST_SUBMIT_STATUS2_ERROR       => '处于审核状态的帖子不可提交',
        self::POST_SUBMIT_STATUS3_ERROR       => '处于发布状态的帖子不可提交',
        self::COMMENT_SUBMIT_STATUS2_ERROR       => '处于审核状态的评论不可提交',
        self::COMMENT_SUBMIT_STATUS3_ERROR       => '处于发布状态的评论不可提交',
        self::POST_CONTENT_WORDS_ERROR       => '帖子内容里存在违规内容',
        self::COMMENT_CONTENT_WORDS_ERROR       => '评论内容里存在违规内容',
        self::MEMBER_EXPIRED_LOGS_ERROR       => '成员已过期，不能发送私信',
        self::COMMENT_PID_ERROR       => 'pid required',
        self::COMMENT_PARENT_ERROR       => '一级评论才能产生草稿',
        self::TYPE_ERROR       => 'type过长',
        self::EMAIL_ERROR       => '邮箱已被注册',
        self::EMAIL_REGEX_ERROR       => '邮箱格式不正确',
        self::PHONE_REGEX_ERROR       => '手机号格式不正确',
        self::PHONE_ERROR       => '手机号已被注册',
        self::EMAIL_EXIST_ERROR       => '邮箱不存在',
        self::PHONE_EXIST_ERROR       => '手机号不存在',
        self::EMAIL_BAND_ERROR       => '已绑定邮箱',
        self::PHONE_BAND_ERROR       => '已绑定手机',
        self::COUNTRY_CODE_ERROR       => '手机区号错误',
        self::MEMBER_ROLE_ERROR             => '该成员无发送消息权限',
        self::TEAMPLAPE_ERROR             => '模板不存在',
        self::MEMBER_ME_ERROR            => '自己不能给自己发送信息',
        self::FILE_OR_MESSAGE_ERROR             => '文件和消息只能传其一',
        self::MEMBER_ERROR             => '对方已注销',
        self::MEMBER_FOLLOW_ERROR             => '需关注对方才能发送消息',
        self::VERIFIED_ERROR             => '需认证才能给对方发消息',
        self::MEMBER_EXPIRED_ERROR             => '成员已过期，不能发送私信',
        self::FILE_OR_FILEINFO_ERROR             => '文件和文件信息只能传其一',
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
        self::GROUP_EXISTS             => '小组不存在',
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
        self::POST_GROUP_ALLOW_ERROR        => '无小组发帖权限',
        self::POST_COMMENTS_POSTS_ERROR        => '评论异常，未找到帖子信息',
        self::POST_COMMENTS_ALLOW_ERROR        => '无小组评论权限',
        self::COMMENTS_LOG_EXIST_ERROR        => '评论异常，草稿表对应的评论未找到',
        self::POSTS_LOG_EXIST_ERROR        => '帖子异常，草稿表对应的帖子未找到',
        self::POSTS_LOG_CHECK_PARAMS_ERROR        => '内容、文件、扩展内容，三种不可全部为空，至少其中一个有值',
        self::EXTENDS_UUID_ERROR        => '存在未知扩展',
        self::CONTENT_COUNT_ERROR        => '内容字数过多',
        self::DELETE_FILES_ERROR        => 'files在使用',
        self::DELETE_EXTEND_ERROR        => 'extend在使用',
        self::FILES_INFO_ERROR        => '请输入正确的fileInfo格式',
        self::EXTENDS_EID_ERROR        => 'extendsJson eid必填',
        self::USER_WALLETS_ERROR        => '用户钱包不存在',
        self::BALANCE_CLOSING_BALANCE_ERROR        => '期末余额和钱包额度不匹配',
        self::USER_BALANCE_ERROR        => '钱包余额不允许支付',
        self::SING_EXPIRED_ERROR        => '签名已过期',
        self::TO_USER_WALLETS_ERROR        => '对方钱包不存在',
        self::TO_BALANCE_CLOSING_BALANCE_ERROR        => '对方期末余额和钱包额度不匹配',


        self::CODE_LOGIN_ERROR                => '用户名或密码错误',
        self::CODE_CAPTCHA_ERROR              => '图片验证码错误',
        self::ACCOUNT_ERROR        => '账号必填',
        self::ADMIN_ACCOUNT_ERROR        => '邮箱或手机号找不到',
        self::BACKEND_PATH_ERROR        => '入口已被占用',
        self::KEYS_NAME_ERROR        => '密钥名称必填',
        self::KEYS_PLAT_ERROR        => '请选择密钥应用平台',
        self::PLUGIN_PLAT_ERROR        => '请选择插件',
        self::DELETE_PACK_ERROR        => '有部分删除无权限',
        self::REPEAT_PARTNER_ERROR        => '无需重复添加',
        self::DELETE_ADMIN        => '不允许删除自己',
        self::LANGUAGE_SETTING_ERROR        => '已存在，不可重复配置该语言',


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
