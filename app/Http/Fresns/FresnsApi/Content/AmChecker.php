<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content;

use App\Base\Checkers\BaseChecker;
use App\Http\Models\Common\ConfigGroup;
use App\Http\Fresns\FresnsUsers\FresnsUsers;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;

// 业务检查, 比如金额，状态等
class AmChecker extends BaseChecker
{
    // 错误码

    public $codeMap = [

    ];

    public static function checkPost($mid)
    {

    }

}
