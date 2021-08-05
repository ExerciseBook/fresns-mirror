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
use App\Http\Fresns\FresnsCommentLogs\FresnsCommentLogsConfig;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
class CommentResourceDetail extends BaseAdminResource
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
            'cid' => $commentInfo['uuid'] ?? "",
            'type' => $this->type,
            'content' => $this->content,
            'isMarkdown' => $this->is_markdown,
            'isAnonymous' => $this->is_anonymous,
            'isPluginEdit' => $this->is_plugin_edit,
            'pluginUnikey' => $this->plugin_unikey,
            // 'editor' => json_decode($this->editor_json,true),
            'location' => json_decode($this->location_json, true) ?? [],
            'files' => json_decode($this->files_json, true) ?? [],
            'extends' => json_decode($this->extends_json, true) ?? [],
        ];
        return $default;
    }
}