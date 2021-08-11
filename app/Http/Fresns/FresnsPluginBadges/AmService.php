<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPluginBadges;


use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesConfig;


class AmService extends BaseAdminService
{
    public function __construct()
    {
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        $common['selectOption'] = AmConfig::TEST_SELECT_OPTION;
        return $common;
    }

    //获取插件
    public static function getPluginExpand($member_id, $type, $langTag)
    {
        $unikeyArr = FresnsPluginBadges::where('member_id', $member_id)->pluck('plugin_unikey')->toArray();
        $payArr = FresnsPluginUsages::whereIn('plugin_unikey', $unikeyArr)->where('type', $type)->get()->toArray();
        $expandsArr = [];
        foreach ($payArr as $v) {
            $item = [];
            $item['plugin'] = $v['plugin_unikey'];
            $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsPluginUsagesConfig::CFG_TABLE, 'name',
                $v['id'], $langTag);
            $item['icon'] = $v['icon_file_url'];
            $plugins = FresnsPlugin::where('unikey', $v['plugin_unikey'])->first();
            $item['url'] = $plugins['access_path'].$v['parameter'];
            $badges = FresnsPluginBadges::where('member_id', $member_id)->where('plugin_unikey',
                $v['plugin_unikey'])->first();
            $item['badgesType'] = $badges['display_type'];
            $item['badgesValue'] = $badges['value_text'];
            $expandsArr[] = $item;
        }

        return $expandsArr;
    }

}