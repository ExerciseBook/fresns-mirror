<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

use App\Base\Checkers\BaseChecker;
use App\Http\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;

class FsChecker extends BaseChecker
{
    // Status Code
    public $codeMap = [

    ];

    public static function checkPost($mid)
    {
    }
}
