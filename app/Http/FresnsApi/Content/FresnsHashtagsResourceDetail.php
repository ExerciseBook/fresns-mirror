<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\AmGlobal\GlobalService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtags;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtagsConfig;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShields;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesService;
use Illuminate\Support\Facades\DB;

class FresnsHashtagsResourceDetail extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(1);
        // form 字段
        $formMap = FresnsGroupsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $mid = GlobalService::getGlobalKey('member_id');
        $langTag = $request->header('langTag');
        $description = ApiLanguageHelper::getLanguages(FresnsHashtagsConfig::CFG_TABLE, 'description', $this->id);
        // $followName = ApiConfigHelper::getConfigByKey(AmConfig::HASHTAG_FOLLOW_NAME);
        // $likeName = ApiConfigHelper::getConfigByKey(AmConfig::HASHTAG_LIKE_NAME);
        // $shieldName = ApiConfigHelper::getConfigByKey(AmConfig::HASHTAG_SHIELD_NAME);
        $cover = ApiFileHelper::getImageSignUrlByFileIdUrl($this->cover_file_id, $this->cover_file_url);

        // 是否关注
        // $followStatus = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',3)->where('follow_id',$this->id)->count();
        $followStatus = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            3)->where('follow_id', $this->id)->count();
        // 是否点赞
        $likeStatus = FresnsMemberLikes::where('member_id', $mid)->where('like_type', 3)->where('like_id',
            $this->id)->count();
        // 是否屏蔽
        // $shieldStatus = FresnsMemberShields::where('member_id',$mid)->where('shield_type',3)->where('shield_id',$this->id)->count();
        $shieldStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type',
            3)->where('shield_id', $this->id)->count();
        // 查询 member_shields 表，该评论的作者是否被我屏蔽。输出 0.未屏蔽 1.已屏蔽"
        // $shieldMemberStatus = FresnsMemberShields::where('member_id',$mid)->where('shield_type',1)->where('shield_id',$this->member_id)->count();
        $shieldMemberStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id',
            $mid)->where('shield_type', 1)->where('shield_id', $this->member_id)->count();
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::SHIELD_HASHTAG_SETTING);
        $likeSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::LIKE_HASHTAG_SETTING);
        $followSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::FOLLOW_HASHTAG_SETTING);
        $hashtagName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::HASHTAG_NAME) ?? '话题';
        $followName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::FOLLOW_HASHTAG_NAME) ?? '收藏';
        $likeName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::LIKE_HASHTAG_NAME) ?? '点赞';
        $shieldName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::SHIELD_HASHTAG_NAME) ?? '不喜欢';
        if (! $langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }
        $seo = DB::table('seo')->where('linked_type', 2)->where('linked_id', $this->id)->where('lang_tag',
            $langTag)->where('deleted_at', null)->first();
        $seoInfo = [];
        if ($seo) {
            $seoInfo['title'] = $seo->title;
            $seoInfo['keywords'] = $seo->keywords;
            $seoInfo['description'] = $seo->description;
        }
        FresnsHashtags::where('id', $this->id)->increment('view_count');
        // 默认字段
        $default = [
            'huri' => $this->slug,
            'hname' => $this->name,
            'cover' => $cover,
            'description' => $description == null ? '' : $description['lang_content'],
            'hashtagName' => $hashtagName,
            'likeSetting' => $likeSetting,
            'likeName' => $likeName,
            'likeStatus' => $likeStatus,
            'followSetting' => $followSetting,
            'followName' => $followName,
            'followStatus' => $followStatus,
            'shieldSetting' => $shieldSetting,
            'shieldName' => $shieldName,
            'shieldStatus' => $shieldStatus,
            'viewCount' => $this->view_count,
            'likeCount' => $this->like_count,
            'followCount' => $this->follow_count,
            'shieldCount' => $this->shield_count,
            'postCount' => $this->post_count,
            'essenceCount' => $this->essence_count,
            // 'seoInfo' => $seoInfo
            // 'followName' => $followName,
            // 'likeName' => $likeName,
            // 'shieldName' => $shieldName,
        ];
        // 合并
        $arr = $default;

        return $arr;
    }
}
