<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Info;

use App\Base\Checkers\BaseChecker;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\FresnsApi\Base\FresnsBaseChecker;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;

class FsChecker extends FresnsBaseChecker
{
    // Status Code
    const EMAIL_ERROR = 30074;
    const EMAIL_REGEX_ERROR = 30075;
    const PHONE_REGEX_ERROR = 30076;
    const PHONE_ERROR = 30077;
    const EMAIL_EXIST_ERROR = 30078;
    const PHONE_EXIST_ERROR = 30079;
    const USER_ERROR = 30056;
    const EMAIL_BAND_ERROR = 30080;
    const PHONE_BAND_ERROR = 30081;
    const COUNTRY_CODE_ERROR = 30082;
    const TEAMPLAPE_ERROR = 30087;
    const PLUGIN_SMS_ERROR = 30127;
    public $codeMap = [
        self::EMAIL_ERROR => '邮箱已被注册',
        self::EMAIL_REGEX_ERROR => '邮箱格式不正确',
        self::PHONE_REGEX_ERROR => '手机号格式不正确',
        self::PHONE_ERROR => '手机号已被注册',
        self::EMAIL_EXIST_ERROR => '邮箱不存在',
        self::PHONE_EXIST_ERROR => '手机号不存在',
        self::USER_ERROR => '用户参数不存在',
        self::EMAIL_BAND_ERROR => '已绑定邮箱',
        self::PHONE_BAND_ERROR => '已绑定手机',
        self::COUNTRY_CODE_ERROR => '手机区号错误',
        self::TEAMPLAPE_ERROR => '模板不存在',
        self::PLUGIN_SMS_ERROR => '未配置插件服务商',
    ];

    // Check Verify Code
    public static function checkVerifyCode($type, $useType, $account)
    {
        // Sending Message Settings Plugin
        if ($type == 1) {
            $pluginUniKey = ApiConfigHelper::getConfigByItemKey('send_email_service');
        } else {
            $pluginUniKey = ApiConfigHelper::getConfigByItemKey('send_sms_service');
        }
        if (empty($pluginUniKey)) {
            return self::checkInfo(self::PLUGIN_SMS_ERROR);
        }
        $countryCode = request()->input('countryCode');
        $template = request()->input('template');
        $templateBlade = ApiConfigHelper::getConfigByItemKey('verifycode_template'.$template);
        if (! $templateBlade) {
            return self::checkInfo(self::TEAMPLAPE_ERROR);
        }
        $templateData = json_decode($templateBlade, true);
        $emailArr = [];
        $phoneArr = [];
        foreach ($templateData as $t) {
            if ($t['type'] == 'email') {
                $emailArr = $t;
            }
            if ($t['type'] == 'sms') {
                $phoneArr = $t;
            }
        }
        if ($type == 1) {
            if (! $emailArr) {
                return self::checkInfo(self::TEAMPLAPE_ERROR);
            }
            if (! $emailArr['isEnable']) {
                return self::checkInfo(self::TEAMPLAPE_ERROR);
            }
        } else {
            if (! $phoneArr) {
                return self::checkInfo(self::TEAMPLAPE_ERROR);
            }
            if (! $phoneArr['isEnable']) {
                return self::checkInfo(self::TEAMPLAPE_ERROR);
            }
        }

        /*
         * Code Usage
         * https://fresns.org/api/info/sendVerifyCode.html
         */
        switch ($useType) {
            // useType=1
            case 1:
                if ($type == 1) {
                    $result = self::RuleEmail($account);
                    if (! $result) {
                        return self::checkInfo(self::EMAIL_REGEX_ERROR);
                    }
                    $count = FresnsUsers::where('email', $account)->count();
                    if ($count > 0) {
                        return self::checkInfo(self::EMAIL_ERROR);
                    }
                } else {
                    if ($countryCode != FsConfig::COUNTRYCODE) {
                        return self::checkInfo(self::COUNTRY_CODE_ERROR);
                    }
                    $result = self::RulePhone($account);
                    if (! $result) {
                        return self::checkInfo(self::PHONE_REGEX_ERROR);
                    }
                    $count = FresnsUsers::where('pure_phone', $account)->count();
                    if ($count > 0) {
                        return self::checkInfo(self::PHONE_ERROR);
                    }
                }
                break;
            // useType=2
            case 2:
                if ($type == 1) {
                    $result = self::RuleEmail($account);
                    if (! $result) {
                        return self::checkInfo(self::EMAIL_REGEX_ERROR);
                    }
                    $count = FresnsUsers::where('email', $account)->count();
                    if ($count == 0) {
                        return self::checkInfo(self::EMAIL_EXIST_ERROR);
                    }
                } else {
                    if ($countryCode != FsConfig::COUNTRYCODE) {
                        return self::checkInfo(self::COUNTRY_CODE_ERROR);
                    }
                    $result = self::RulePhone($account);
                    if (! $result) {
                        return self::checkInfo(self::PHONE_REGEX_ERROR);
                    }
                    $count = FresnsUsers::where('pure_phone', $account)->count();
                    if ($count == 0) {
                        return self::checkInfo(self::PHONE_EXIST_ERROR);
                    }
                }
                break;
            // useType=3
            case 3:
                if (empty(request()->header('uid'))) {
                    return self::checkInfo(self::USER_ERROR);
                }
                if ($type == 1) {
                    $result = self::RuleEmail($account);
                    if (! $result) {
                        return self::checkInfo(self::EMAIL_REGEX_ERROR);
                    }
                    $userInfo = FresnsUsers::where('uuid', request()->header('uid'))->first();
                    if (empty($userInfo)) {
                        return self::checkInfo(self::USER_ERROR);
                    }
                    if ($userInfo['email']) {
                        return self::checkInfo(self::EMAIL_BAND_ERROR);
                    }
                } else {
                    if ($countryCode != FsConfig::COUNTRYCODE) {
                        return self::checkInfo(self::COUNTRY_CODE_ERROR);
                    }
                    $result = self::RulePhone($account);
                    if (! $result) {
                        return self::checkInfo(self::PHONE_REGEX_ERROR);
                    }
                    $userInfo = FresnsUsers::where('uuid', request()->header('uid'))->first();
                    if (empty($userInfo)) {
                        return self::checkInfo(self::USER_ERROR);
                    }
                    if ($userInfo['pure_phone']) {
                        return self::checkInfo(self::PHONE_BAND_ERROR);
                    }
                }
                break;
            // useType=4
            case 4:
                if (empty(request()->header('uid'))) {
                    return self::checkInfo(self::USER_ERROR);
                }
                $userInfo = FresnsUsers::where('uuid', request()->header('uid'))->first();
                if (empty($userInfo)) {
                    return self::checkInfo(self::USER_ERROR);
                }
                if ($type == 1) {
                    if (! $userInfo['email']) {
                        return self::checkInfo(self::EMAIL_EXIST_ERROR);
                    }
                } else {
                    if (! $userInfo['pure_phone']) {
                        return self::checkInfo(self::PHONE_EXIST_ERROR);
                    }
                }
                break;
            // default
            default:
                if ($type == 1) {
                    $result = self::RuleEmail($account);
                    if (! $result) {
                        return self::checkInfo(self::EMAIL_REGEX_ERROR);
                    }
                } else {
                    if ($countryCode != FsConfig::COUNTRYCODE) {
                        return self::checkInfo(self::COUNTRY_CODE_ERROR);
                    }
                    $result = self::RulePhone($account);
                    if (! $result) {
                        return self::checkInfo(self::PHONE_REGEX_ERROR);
                    }
                }
                break;
        }
    }

    public static function RulePhone($phone)
    {
        $result = preg_match("/^1[34578]{1}\d{9}$/", $phone);

        return $result;
    }

    public static function RuleEmail($email)
    {
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
        preg_match($pattern, $email, $matches);

        return $matches;
    }
}
