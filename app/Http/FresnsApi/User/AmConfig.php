<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\User;

class AmConfig
{
    //钱包type = 1 支付
    const PLUGIN_USAGERS_TYPE_1 = 1;
    //钱包type = 2 提现
    const PLUGIN_USAGERS_TYPE_2 = 2;

    //主角色
    const MEMBER_ROLE_REL_TYPE_2 = 2;

    //密码校验规则
    const PASSWORD_NUMBER = 1; //数字
    const PASSWORD_LOWERCASE_LETTERS = 2; //小写字母
    const PASSWORD_CAPITAL_LETTERS = 4; //大写字母
    const PASSWORD_SYMBOL = 3; //符号
}
