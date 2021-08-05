<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsPostAppends\FresnsPostAppends;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberIcons\FresnsMemberIcons;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsApi\Info\AmService;
use App\Http\Fresns\FresnsComments\FresnsCommentsConfig;
use App\Http\Fresns\FresnsPosts\FresnsPosts;
use App\Http\Fresns\FresnsCommentAppends\FresnsCommentAppends;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;
use App\Http\Fresns\FresnsExtendLinkeds\FresnsExtendLinkeds;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\Fresns\FresnsApi\Content\AmConfig;
use App\Http\Fresns\FresnsExtends\FresnsExtendsConfig;

class FresnsExtendsResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(1);
        // form 字段
        $formMap = FresnsExtendsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $sourceContent = [];
        $extendLinkeds = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('extend_id', $this->id)->first();
        $sourceContent['type'] = $extendLinkeds->type;
        $sourceContent['id'] = $extendLinkeds->linked_id;
        $sourceContent['title'] = "";
        $sourceContent['content'] = "";
        $sourceContent['status'] = "";
        if ($extendLinkeds->type == 1) {
            $posts = FresnsPosts::find($extendLinkeds->linked_id);
            $sourceContent['title'] = $posts['title'];
            $sourceContent['content'] = $posts['content'];
            $sourceContent['status'] = $posts['is_enable'];
        }
        if ($extendLinkeds->type == 2) {
            $comment = FresnsComments::find($extendLinkeds->linked_id);
            $sourceContent['content'] = $comment['content'];
            $sourceContent['status'] = $comment['is_enable'];
        }

        $default = [
            'eid' => $this->uuid,
            'plugin' => $this->plugin_unikey,
            'target' => $this->extend_target,
            'value' => $this->extend_value,
            'frame' => $this->frame,
            'position' => $this->position,
            'cover' => $this->cover_file_url,
            'content' => $this->content,
            'title' => $this->title,
            'titleColor' => $this->title_color,
            'descPrimary' => $this->desc_primary,
            'descPrimaryColor' => $this->desc_primary_color,
            'descSecondary' => $this->desc_secondary,
            'descSecondaryColor' => $this->desc_secondary_color,
            'descSecondary' => $this->desc_secondary,
            'descSecondaryColor' => $this->desc_secondary_color,
            'btnName' => $this->btn_name,
            'btnColor' => $this->btn_color,
            'moreJson' => [],
            'sourceContent' => $sourceContent,
        ];
        // 合并
        $arr = $default;

        return $arr;
    }

    public function GetDistance($lat1, $lng1, $lat2, $lng2)
    {
        $EARTH_RADIUS = 6378.137;

        $radLat1 = $this->rad($lat1);
        $radLat2 = $this->rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = $this->rad($lng1) - $this->rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * $EARTH_RADIUS;
        $s = round($s * 10000) / 10000;

        return $s;
    }

    private function rad($d)
    {
        return $d * M_PI / 180.0;
    }
}

