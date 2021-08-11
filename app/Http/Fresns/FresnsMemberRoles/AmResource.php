<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMemberRoles;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;

class AmResource extends BaseAdminResource
{

    public function toArray($request)
    {
        // 默认字段
        $arr = [
            'id' => $this->id,
            'type' => $this->type,
            'name' => FresnsLanguagesService::getLanguageByTableId(FresnsMemberRolesConfig::CFG_TABLE, 'name', $this->id),
            'icon' => ApiFileHelper::getImageSignUrlByFileIdUrl($this->icon_file_id, $this->icon_file_url),
            'permission' => json_decode($this->permission,true),
        ];

        return $arr;
    }
}

