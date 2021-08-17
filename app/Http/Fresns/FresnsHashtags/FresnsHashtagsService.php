<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 系统解耦, 快捷方式入口
namespace App\Http\Fresns\FresnsHashtags;

use App\Http\Fresns\FresnsApi\Base\FresnsBaseService;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesService;
use Illuminate\Support\Facades\DB;

class FresnsHashtagsService extends FresnsBaseService
{
    public $needCommon = true;

    public function __construct()
    {
        $this->config = new AmConfig();
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        // $common =  parent::common();
        $id = request()->input('huri');
        $langTag = request()->header('langTag');
        $mid = request()->header('mid');
        $group = FresnsHashtags::where('slug', $id)->first();
        $common['seoInfo'] = [];
        if (!$langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }
        // $seo = null;
        if ($group) {
            $seo = DB::table('seo')->where('linked_type', 3)->where('linked_id', $group['id'])->where('lang_tag',
                $langTag)->where('deleted_at', null)->first();
            $seoInfo = [];
            if ($seo) {
                $seoInfo['title'] = $seo->title;
                $seoInfo['keywords'] = $seo->keywords;
                $seoInfo['description'] = $seo->description;
                $common['seoInfo'] = $seoInfo;
            }
        }
        $common['seoInfo'] = (Object)$common['seoInfo'];
        
        return $common;
    }
}
