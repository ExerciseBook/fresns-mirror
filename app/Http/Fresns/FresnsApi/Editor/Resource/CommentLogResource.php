<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Editor\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsCommentLogs\FresnsCommentLogsConfig;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsPostLogs\FresnsPostLogsConfig;
use App\Http\Fresns\FresnsPosts\FresnsPosts;

class CommentLogResource extends BaseAdminResource
{
    public function toArray($request)
    {
        $formMap = FresnsCommentLogsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $commentInfo = FresnsComments::find($this->comment_id);
        $default = [
            'id' => $this->id,
            'cid' => $commentInfo['uuid'] ?? '',
            'content' => mb_substr($this->content, 0, 140),
            'reason' => $this->reason,
            'submitTime' => $this->submit_at,
            'time' => $this->created_at,
        ];

        return $default;
    }
}
