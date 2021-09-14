<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\User;

class FsConfig
{
    // Wallet Type
    const PLUGIN_USAGERS_TYPE_1 = 1;
    const PLUGIN_USAGERS_TYPE_2 = 2;

    // Main Role
    const MEMBER_ROLE_REL_TYPE_2 = 2;

    // Password verification rules
    const PASSWORD_NUMBER = 1; // Digital
    const PASSWORD_LOWERCASE_LETTERS = 2; // Lowercase letters
    const PASSWORD_CAPITAL_LETTERS = 3; // Capital letters
    const PASSWORD_SYMBOL = 4; // Symbols
}
