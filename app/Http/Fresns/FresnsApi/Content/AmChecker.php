<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content;

use App\Base\Checkers\BaseChecker;

use App\Http\Models\Common\ConfigGroup;

// use App\Plugins\Tweet\TweetUsers\TweetUsers;
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
    const MEMBER_ROLE_ERROR = 30027;
    const MEMBER_ERROR = 30028;
    const MEMBER_FOLLOW_ERROR = 30029;
    const FILE_OR_MESSAGE_ERROR = 30030;
    const FILES_ERROR = 30031;
    const DIALOG_WORD_ERROR = 30032;
    const VERIFIED_ERROR = 30033;
    public $codeMap = [
        self::MEMBER_ROLE_ERROR => '该成员无发送消息权限',
        self::MEMBER_ERROR => '对方已注销',
        self::MEMBER_FOLLOW_ERROR => '需关注对方才能发送消息',
        self::FILE_OR_MESSAGE_ERROR => '文件和消息只能传其一',
        self::FILES_ERROR => '文件不存在',
        self::DIALOG_WORD_ERROR => '存在屏蔽字，禁止发送',
        self::VERIFIED_ERROR => '需认证才能给对方发消息',
    ];

    public static function checkPost($mid)
    {

    }

}
