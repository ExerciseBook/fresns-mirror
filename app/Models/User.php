<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;
    use Traits\UserServiceTrait;

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function stat()
    {
        return $this->hasOne(UserStat::class);
    }

    public function archives()
    {
        return $this->hasMany(UserArchive::class);
    }

    public function mainRole()
    {
        return $this->hasOne(UserRole::class)->where('is_main', 1);
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }
}
