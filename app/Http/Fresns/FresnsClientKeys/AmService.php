<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsClientKeys;

use App\Base\Services\BaseAdminService;

// use App\Plugins\Tweet\TweetConfigs\TweetConfigs;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;

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
        $common['IsEnableOption'] = AmConfig::IS_ENABLE_OPTION;
        $common['typeOption'] = AmConfig::TYPE_OPTION;
        $platfroms = FresnsConfigs::where('item_key', AmConfig::PLATFORMS)->first(['item_value']);
        // dd($lang_code);
        $platfroms_arr = json_decode($platfroms['item_value'], true);
        // dd($platfroms_arr);
        $array = [];
        foreach ($platfroms_arr as $v) {
            $arr = [];
            $arr['key'] = $v['id'];
            $arr['text'] = $v['name'];
            $array[] = $arr;
        }
        // $common['platformOption'] = AmConfig::PLATFORM_OPTION;
        $common['platformOption'] = $array;

        return $common;
    }

}