<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsImplants;

use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;

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

    public static function getImplants($size, $pageSize, $type)
    {
        $startNum = ($size - 1) * $pageSize;
        $endNum = $size * $pageSize;
        $data = FresnsImplants::where('implant_type', $type)->where('position', '>=', $startNum)->where('position', '<',
            $endNum)->get(['id', 'implant_template', 'type', 'target', 'value', 'support', 'position']);
        // dd($data);
        // 判断是否过期
        if ($data) {
            foreach ($data as &$v) {
                $name = ApiLanguageHelper::getLanguages(AmConfig::CFG_TABLE, 'name', $v['id']);
                $v['template'] = $v['implant_template'];
                $v['name'] = $name == null ? "" : $name['lang_content'];
                $v['position'] = $v['position'];
                $v['pageType'] = $v['type'];
                $v['pageTarget'] = $v['target'];
                $v['pageValue'] = $v['value'];
                $v['pageSupport'] = $v['support'];
                $v['position'] = $v['position'];
                if ($v['expired_at'] && $v['expired_at'] < date('Y-m-d H:i:s', time())) {
                    unset($v);
                }
                // unset($v['id']);
                unset($v['implant_template']);
                unset($v['type']);
                unset($v['target']);
                unset($v['value']);
                unset($v['support']);
            }
        }
        return $data;
    }
}