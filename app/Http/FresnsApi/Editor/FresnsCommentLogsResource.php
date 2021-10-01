<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Editor;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogsConfig;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogsConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;

/**
 * List resource config handle.
 */
class FresnsCommentLogsResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = FresnsCommentLogsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        // Comment Info
        $commentInfo = FresnsComments::find($this->comment_id);

        // Default Field
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
