<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class AccountWallet extends Model
{
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
