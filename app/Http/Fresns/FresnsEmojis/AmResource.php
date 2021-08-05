<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsEmojis;

use App\Base\Resources\BaseAdminResource;
use App\Plugins\Tweet\TweetFiles\TweetFilesService;
use App\Plugins\Tweet\TweetLanguages\TweetLanguagesService;

class AmResource extends BaseAdminResource
{

    public function toArray($request)
    {
        // form 字段
        $formMap = AmConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        $count = 0;
        if ($this->parent_id < 0) {
            $count = AmModel::where('parent_id', $this->id)->count();
        }

        $langArr = TweetLanguagesService::getLanguages(AmConfig::CFG_TABLE, 'name', $this->id);

        // 默认字段
        $default = [
            'key' => $this->id,
            'id' => $this->id,
            'is_enable' => boolval($this->is_enable),
            'disabled' => false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'count' => $count,
            'image_file_url' => TweetFilesService::getImageSignUrlByFileIdUrl($this->image_file_id,$this->image_file_url),
            'lang_json_arr' => $langArr,
            'nickname' => $this->nickname,
            'more_json' => $this->more_json,
            'more_json_decode' => json_decode($this->more_json, true),
        ];

        // 合并
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}

