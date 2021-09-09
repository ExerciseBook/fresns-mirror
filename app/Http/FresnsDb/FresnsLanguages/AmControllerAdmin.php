<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsLanguages;

use App\Base\Controllers\BaseAdminController;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{
    public function __construct()
    {
        $this->service = new AmService();
    }

    // Configure new multi-language
    public function configStore(Request $request)
    {
        $tableName = $request->input('table_name');
        $tableField = $request->input('table_field');
        $tableKey = $request->input('table_key');
        $langJson = $request->input('lang_json');
        $langArr = json_decode($langJson, true);
        AmModel::where('table_key', $tableKey)->delete();
        $itemArr = [];
        foreach ($langArr as $v) {
            $item = [];
            $item['table_name'] = $tableName;
            $item['table_field'] = $tableField;
            $item['table_key'] = $tableKey;
            $tag = AmService::conversionLangTag($v['lang_tag']);
            $langCode = $tag['lang_code'];
            $areaCode = $tag['area_code'];
            $item['lang_code'] = $langCode;
            $item['area_code'] = $areaCode ?? null;
            $item['lang_tag'] = $v['lang_tag'];
            $item['lang_content'] = $v['lang_content'];
            $itemArr[] = $item;
        }

        AmModel::insert($itemArr);

        $this->success();
    }

    // Validation Rules
    public function rules($ruleType)
    {
        $rule = [];

        $config = new AmConfig($this->service->getTable());

        switch ($ruleType) {
            case AmConfig::RULE_STORE:
                $rule = $config->storeRule();
                break;

            case AmConfig::RULE_UPDATE:
                $rule = $config->updateRule();
                break;

            case AmConfig::RULE_DESTROY:
                $rule = $config->destroyRule();
                break;

            case AmConfig::RULE_DETAIL:
                $rule = $config->detailRule();
                break;
        }

        return $rule;
    }
}
