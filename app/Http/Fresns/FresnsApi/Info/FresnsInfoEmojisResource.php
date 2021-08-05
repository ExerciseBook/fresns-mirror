<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Info;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsEmojis\FresnsEmojis;
use App\Http\Fresns\FresnsEmojis\FresnsEmojisConfig;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;

class FresnsInfoEmojisResource extends BaseAdminResource
{

    public function toArray($request)
    {
        $emojisArr = FresnsEmojis::where('is_enable', 1)->where('parent_id', $this->id)->get([
            'code',
            'image_file_url',
            'name',
            'image_file_id'
        ])->toArray();
        $itemArr = [];
        foreach ($emojisArr as $v) {
            $item = [];
            $item['code'] = $v['code'];
            // $item['name'] = $v['name'];
            $item['image'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['image_file_id'], $v['image_file_url']);
            $itemArr[] = $item;
        }

        // dd($itemArr);

        // 默认字段
        $default = [
            'name' => FresnsLanguagesService::getLanguageByTableId(FresnsEmojisConfig::CFG_TABLE, 'name', $this->id),
            'image' => ApiFileHelper::getImageSignUrlByFileIdUrl($this->image_file_id, $this->image_file_url),
            'count' => count($itemArr),
            'emoji' => $itemArr,
        ];


        return $default;
    }
}

