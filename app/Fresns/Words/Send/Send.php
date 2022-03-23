<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Send;

use App\Fresns\Words\Send\DTO\SendAppNotification;
use App\Fresns\Words\Send\DTO\SendEmailDTO;
use App\Fresns\Words\Send\DTO\SendSmsDTO;
use App\Fresns\Words\Send\DTO\SendWechatMessage;
use App\Helpers\ConfigHelper;
use Fresns\CmdWordManager\Exceptions\Constants\ExceptionConstant;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;

class Send
{
    use CmdWordResponseTrait;

    /**
     * @param $wordBody
     * @return string
     *
     * @throws \Throwable
     */
    public function sendEmail($wordBody)
    {
        $dtoWordBody = new SendEmailDTO($wordBody);

        $this->ensureUnikeyIsNotEmpry(
            $pluginUniKey = ConfigHelper::fresnsConfigByItemKey('send_email_service')
        );

        return \FresnsCmdWord::plugin($pluginUniKey)->sendEmail($wordBody)->getCode();
    }

    /**
     * @param $wordBody
     * @return mixed
     *
     * @throws \Throwable
     */
    public function sendSms($wordBody)
    {
        $wordBody = new SendSmsDTO($wordBody);

        $this->ensureUnikeyIsNotEmpry(
            $pluginUniKey = ConfigHelper::fresnsConfigByItemKey('send_sms_service')
        );

        return \FresnsCmdWord::plugin($pluginUniKey)->sendSms($wordBody);
    }

    /**
     * @param $wordBody
     * @return string
     *
     * @throws \Throwable
     */
    public function sendAppNotification($wordBody)
    {
        $wordBody = new SendAppNotification($wordBody);

        $channelMap = [
            1 => 'send_ios_service',
            2 => 'send_android_service',
        ];

        $itemKey = $channelMap[$wordBody->channel];

        $this->ensureUnikeyIsNotEmpry(
            $pluginUniKey = ConfigHelper::fresnsConfigByItemKey($itemKey)
        );

        return \FresnsCmdWord::plugin($pluginUniKey)->sendAppNotification($wordBody);
    }

    /**
     * @param $wordBody
     * @return string
     *
     * @throws \Throwable
     */
    public function sendWechatMessage($wordBody)
    {
        $wordBody = new SendWechatMessage($wordBody);

        $this->ensureUnikeyIsNotEmpry(
            $pluginUniKey = ConfigHelper::fresnsConfigByItemKey('send_wechat_service')
        );

        return \FresnsCmdWord::plugin($pluginUniKey)->sendWechatMessage($wordBody);
    }

    protected function ensureUnikeyIsNotEmpry(string $string)
    {
        if (empty($pluginUniKey)) {
            ExceptionConstant::getHandleClassByCode(ExceptionConstant::ERROR_CODE_20004)::throw();
        }
    }
}
