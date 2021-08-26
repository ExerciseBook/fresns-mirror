<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\Fresns\FresnsMembers\FresnsMembers;

class FresnsPostMembersResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(1);
        // form 字段
        $formMap = FresnsMemberLikesConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $memberInfo = FresnsMembers::find($this->member_id);
        // 默认字段
        $default = [
            'mid' => $memberInfo['id'],
            'mname' => $memberInfo['name'],
            'nickname' => $memberInfo['nickname'],
            'avatar' => $memberInfo['decorate_file_url'],
            'gender' => $memberInfo['gender'],
            'verifiedStatus' => $memberInfo['verified_status'],
            'verifiedIcon' => $memberInfo['verified_file_url'],
        ];
        // 合并
        $arr = $default;

        return $arr;
    }
}
