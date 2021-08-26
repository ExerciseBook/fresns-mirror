<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Base;

use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\LogService;

class FresnsApiCode extends ErrorCodeService
{
    const CODE_OK = 0;
    const CODE_FAIL = 30001;
    const CODE_PARAM_ERROR = 30002;
    const CODE_LOGIN_ERROR = 30003;
    const CODE_EXCEPTION = 30004;
    const CODE_FILE_UPLOAD_ERROR = 30005;
    const NO_RECORD = 30006;
    const PARAMS_IDS_INVALID = 30007;
    const NO_PERMISSION = 30008;
    const PERMISSION_NOT_EXIST = 30009;
    const NOT_IMPLEMENTS = 30010;
    const CATEGORY_IDS_INVALID = 30011;
    const TAG_IDS_INVALID = 30012;
    const TOPIC_IDS_INVALID = 30013;
    const CODE_CAPTCHA_ERROR = 30014;
    const CODE_PHONE_ERROR = 30015;
    const CODE_PHONE_LOGIN_ERROR = 30016;
    const CODE_REGISTER_PASSWORD_ERROR = 30017;
    const CODE_EMAIL_LOGIN_ERROR = 30018;
    const CODE_IS_EMAIL = 30019;
    const CODE_SMS_ERROR = 30020;
    const PASSWORD_INVALID = 30021;
    const ACCOUNT_NAME = 30022;
    const ACCOUNT_LOGIN_NAME = 30028;
    const CODE_PHONE_UPDATE = 30023;
    const CODE_LOG_IN_ERROR = 30024;
    const CODE_SEND_SMS_ERROR = 30025;
    const FORGET_NAME_ERROR = 30026;
    const CODE_SEND_SMS_TIME_ERROR = 30027;
    const CODE_PHONE_CODE_ERROR = 30041;
    const CODE_EMAIL_CODE_ERROR = 30042;
    const CODE_LOGIN_ERROR_USER = 30043;
    const TABLE_NOT_EXIST = 30044;

    const PLUGIN_NOT_FOUND = 30045;

    const DELETE_ITEM_ID_ARR_ERROR = 30046;
    const BEGIN_OR_END_TIME_ERROR = 30047;
    const MAX_COUNT_ERROR = 30048;

    const UPDATE_STATUS_ERROR = 30051;
    const SMS_CODE_EXPIRED_ERROR = 30089;
    const SMS_CODE_CHECK_ERROR = 30090;

    const USER_CONSULTANT_COUNT_ERROR = 31115;
    const SHOP_ERROR = 31116;
    const SHOP_PAYMENT_ERROR = 31117;
    const EMAIL_LOGIN_ERROR = 31118;
    const EMAIL_ACTIVE_ERROR = 31121;
    const COIN_ERROR = 311212;
    const NOTIFY_ERROR = 311213;

    private static $CODE_MSG_MAP = [
        self::CODE_OK => 'ok',
        self::CODE_FAIL => '请求异常,请稍后再试',
        self::CODE_PARAM_ERROR => '参数错误',
        self::CODE_LOGIN_ERROR => '用户名或密码错误',
        self::CODE_EXCEPTION => 'exception',
        self::CODE_FILE_UPLOAD_ERROR => 'file upload error',
        self::NO_RECORD => '记录不存在',
        self::PARAMS_IDS_INVALID => 'id(s) params invalid',
        self::NO_PERMISSION => 'no permission',
        self::PERMISSION_NOT_EXIST => 'permission not exist',
        self::NOT_IMPLEMENTS => 'not implements',
        self::TAG_IDS_INVALID => 'tag id(s) invalid',
        self::CODE_CAPTCHA_ERROR => '图片验证码错误',
        self::CODE_PHONE_ERROR => '该用户未注册',
        self::CODE_PHONE_LOGIN_ERROR => '该手机号已被注册',
        self::CODE_REGISTER_PASSWORD_ERROR => '两次输入密码不一致',
        self::CODE_EMAIL_LOGIN_ERROR => '该邮箱已被注册',
        self::CODE_IS_EMAIL => '邮箱格式不对',
        self::CODE_SMS_ERROR => '短信验证码错误',
        self::PASSWORD_INVALID => '密码不正确',
        self::ACCOUNT_NAME => '该昵称已被注册',
        self::CODE_PHONE_UPDATE => '请输入原手机号',
        self::CODE_LOG_IN_ERROR => '请先登录',
        self::CODE_SEND_SMS_ERROR => '短信发送错误',
        self::FORGET_NAME_ERROR => '用户名或邮箱错误',
        self::CODE_SEND_SMS_TIME_ERROR => '验证码已过期，请重新获取',
        self::ACCOUNT_LOGIN_NAME => '该用户名已被注册',
        self::TABLE_NOT_EXIST => '数据表不存在',
        self::DELETE_ITEM_ID_ARR_ERROR => '存在关联数据，不允许删除',
        self::BEGIN_OR_END_TIME_ERROR => '请选择正确时间范围',
        // self::RANDOM_FAIL => '生成失败，请查看日志',
        self::MAX_COUNT_ERROR => '超出最大数量值',
        // self::SMS_NOT_PHONE_ERROR => '当前手机号不存在',
        self::SMS_CODE_EXPIRED_ERROR => '验证码已过期',
        self::SMS_CODE_CHECK_ERROR => '验证码不正确',
        self::EMAIL_ACTIVE_ERROR => '请先去邮箱激活',
        self::NOTIFY_ERROR => '只能删除自己的信息',
    ];

    public static function getMsg($code, $data = [])
    {
        if (! isset(self::$CODE_MSG_MAP[$code])) {
            return 'unknown code';
        }

        // 关于参数错误的信息具体化
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
