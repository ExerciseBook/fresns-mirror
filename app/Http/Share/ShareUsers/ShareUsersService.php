<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 系统解耦, 快捷方式入口
namespace App\Http\Share\ShareUsers;

use App\Http\Share\Common\LogService;
use App\Servers\AccountServer\AmCenterUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShareUsersService extends AmService
{

}

