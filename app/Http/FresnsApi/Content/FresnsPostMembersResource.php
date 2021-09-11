<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;

/**
 * List resource config handle
 */

class FresnsPostMembersResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = FresnsMemberLikesConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $memberInfo = FresnsMembers::find($this->member_id);

        // Default Field
        $default = [
            'mid' => $memberInfo['uuid'],
            'mname' => $memberInfo['name'],
            'nickname' => $memberInfo['nickname'],
            'avatar' => $memberInfo['decorate_file_url'],
            'gender' => $memberInfo['gender'],
            'verifiedStatus' => $memberInfo['verified_status'],
            'verifiedIcon' => $memberInfo['verified_file_url'],
        ];
        
        // Merger
        $arr = $default;

        return $arr;
    }
}
