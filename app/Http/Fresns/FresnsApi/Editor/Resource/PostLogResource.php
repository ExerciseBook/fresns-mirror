<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Editor\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsPostLogs\FresnsPostLogsConfig;
use App\Http\Fresns\FresnsPosts\FresnsPosts;

class PostLogResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(1);

        $formMap = FresnsPostLogsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $postInfo = FresnsPosts::find($this->post_id);
        $default = [
            'id' => $this->id,
            'pid' => $postInfo['uuid'] ?? '',
            'type' => $this->type,
            'title' => $this->title,
            'content' => mb_substr($this->content, 0, 140),
            'reason' => $this->reason,
            'submitTime' => $this->submit_at,
            'time' => $this->created_at,
        ];

        return $default;
    }
}
