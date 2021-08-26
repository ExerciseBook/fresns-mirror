<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Base;

use App\Base\Checkers\BaseChecker;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsSessionTokens\FresnsSessionTokens;
use App\Http\Share\Common\ErrorCodeService;

class AmChecker extends BaseChecker
{
    //校验私有模式下uid和mid必传
    public static function checkSiteMode()
    {
        $siteMode = ApiConfigHelper::getConfigByItemKey('site_mode');

        if ($siteMode == 'private') {
            $uid = request()->header('uid');
            $mid = request()->header('mid');
            if (empty($uid) || empty($mid)) {
                return false;
            }
        }

        return true;
    }

    //校验该成员是否在用户下
    public static function checkUserMember($mid, $uid)
    {
        $memberIdArr = FresnsMembers::where('user_id', $uid)->pluck('id')->toArray();
        if (! in_array($mid, $memberIdArr)) {
            return false;
        }

        return true;
    }

    //校验用户,成员权限
    public static function checkUserMemberPermissions($mid, $uid, $token)
    {
        $platform = request()->header('platform');
        if (! empty($mid)) {
            $userToken = FresnsSessionTokens::where('user_id', $uid)
                ->where('member_id', $mid)
                ->where('platform_id', $platform)
                ->value('token');
            if ($userToken != $token) {
                self::checkInfo(ErrorCodeService::USER_TOKEN_ERROR);
            }
        } else {
            $userToken = FresnsSessionTokens::where('user_id', $uid)
                ->where('member_id', null)
                ->where('platform_id', $platform)
                ->value('token');
            if ($userToken != $token) {
                self::checkInfo(ErrorCodeService::USER_TOKEN_ERROR);
            }
        }

        return true;
    }
}
