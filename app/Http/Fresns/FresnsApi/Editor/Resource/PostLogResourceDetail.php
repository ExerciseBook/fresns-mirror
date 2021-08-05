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

class PostLogResourceDetail extends BaseAdminResource
{


    public function toArray($request)
    {
        $formMap = FresnsPostLogsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $postInfo = FresnsPosts::find($this->post_id);
        // dd(json_decode($this->editor_json,true));
        $default = [
            'id' => $this->id,
            'pid' => $postInfo['uuid'] ?? "",
            'gid' => $this->group_id,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'isMarkdown' => $this->is_markdown,
            'isAnonymous' => $this->is_anonymous,
            'editor' => json_decode($this->editor_json, true) ?? [],
            'allow' => json_decode($this->allow_json, true) ?? [],
            'commentSetting' => json_decode($this->comment_set_json, true) ?? [],
            'location' => json_decode($this->location_json, true) ?? [],
            'files' => json_decode($this->files_json, true) ?? [],
            'extends' => json_decode($this->extends_json, true) ?? [],
        ];
        return $default;
    }
}