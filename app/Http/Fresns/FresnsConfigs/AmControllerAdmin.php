<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsConfigs;

use App\Base\Controllers\BaseAdminController;
use App\Helpers\CommonHelper;
use App\Helpers\StrHelper;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\ValidateService;
use App\Http\Center\Base\FresnsCode;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Plugins\Tweet\TweetLanguages\TweetLanguages;
use App\Plugins\Tweet\TweetPlugin\TweetPlugin;
use App\Plugins\Tweet\TweetPlugin\TweetPluginConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Fresns\FresnsConfigs\FresnsConfigService;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    public function index(Request $request)
    {
        $item_key = $request->input('item_key');
        $languageArr = FresnsConfigService::getLanguageStatus();
        $languagesOption = $languageArr['languagesOption'];

        if ($item_key == AmConfig::LANG_SETTINGS) {
            parent::index($request);
        } else {
            $itemArr = AmModel::where('item_tag', $item_key)->get([
                'item_key',
                'item_value',
                'item_tag',
                'item_type'
            ])->toArray();
            $arr = [];
            foreach ($itemArr as $v) {
                $item = [];
                $item['alias_key'] = $v['item_key'];
                $item['item_type'] = $v['item_type'];
                $item['item_value'] = $v['item_value'];
                if ($v['item_type'] == 'checkbox' || $v['item_type'] == 'select') {
                    if (strstr($item['item_value'], ',')) {
                        $item['item_value'] = explode(',', $v['item_value']);
                    }
                }
                if ($v['item_type'] != 'file') {
                    if ($v['item_value'] == 'true') {
                        $item['item_value'] = true;
                    }
                    if ($v['item_value'] == 'false') {
                        $item['item_value'] = false;
                    }
                }
                //查询对应的语言
                $langMap = TweetLanguages::where('table_name', FresnsConfigsConfig::CFG_TABLE)->where('table_key',
                    $v['item_key'])->pluck('lang_content', 'lang_tag')->toArray();
                $languageArr = [];
                if ($langMap) {
                    foreach ($languagesOption as $languages) {
                        $it = [];
                        $it['key'] = $languages['key'];
                        $it['text'] = $languages['text'];
                        $it['lang_content'] = $langMap[$languages['key']] ?? '';
                        $languageArr[] = $it;
                    }
                }

                $item['lang_json_arr'] = $languageArr;
                $arr[] = $item;

            }
            $list = [];
            if (!empty($arr)) {
                $list['id'] = 1;
                $list['key'] = 1;

                $list['item_key'] = $item_key;
                $list['item_value_decode'] = $arr;
                $list = [$list];
            }

            $data['list'] = $list;
            // common 数据
            $data['common'] = $this->service->common();

            //分页
            $data['pagination']['total'] = empty($arr) ? 0 : 1;
            $data['pagination']['current'] = 1;
            $data['pagination']['pageSize'] = 30;

            $this->success($data);
        }


    }

    public function update(Request $request)
    {
        $json = $request->input('config_json');
        $configArr = json_decode($json, true);

        $this->service->updateConfigs($configArr);

        $this->success();
    }

    //新增语言设置
    public function storeLang(Request $request)
    {
        $rankNum = $request->input('rankNum', null);
        $langCode = $request->input('langCode', null);
        $langName = $request->input('langName', null);
        $writingDirection = $request->input('writingDirection', null);
        $language_status = $request->input('language_status', null);
        $continents = $request->input('continents', null);
        $areaCode = $request->input('areaCode', null);
        $areaName = $request->input('areaName', null);
        $dateFormat = $request->input('dateFormat', null);
        $timeFormatMinute = $request->input('timeFormatMinute', null);
        $timeFormatHour = $request->input('timeFormatHour', null);
        $timeFormatDay = $request->input('timeFormatDay', null);
        $timeFormatMonth = $request->input('timeFormatMonth', null);
        $lengthUnits = $request->input('lengthUnits', null);

        $data = [
            'rankNum' => $rankNum,
            'langCode' => $langCode,
            'langName' => $langName,
            'langTag' => $langCode.'-'.$areaCode,
            'writingDirection' => $writingDirection,
            'language_status' => $language_status,
            'continents' => $continents,
            'areaCode' => $areaCode,
            'areaName' => $areaName,
            'lengthUnits' => $lengthUnits,
            'dateFormat' => $dateFormat,
            'timeFormatMinute' => $timeFormatMinute,
            'timeFormatHour' => $timeFormatHour,
            'timeFormatDay' => $timeFormatDay,
            'timeFormatMonth' => $timeFormatMonth,
            'key' => StrHelper::randString(6)
        ];
        if (empty($data['areaCode'])) {
            $data['langTag'] = $langCode;
        }
        //查询是否有语言设置
        $langSettings = AmModel::where('item_key', AmConfig::LANG_SETTINGS)->first();
        if ($langSettings) {
            $oldLangSettingJson = $langSettings['item_value'];
            $oldLangSettingArr = json_decode($oldLangSettingJson, true);
            $isTrue = true;
            if ($oldLangSettingArr) {
                foreach ($oldLangSettingArr as $v) {
                    if ($v['langTag'] == $data['langTag']) {
                        $isTrue = false;
                    }
                }
            }

            if ($isTrue == true) {
                $oldLangSettingArr[] = $data;
                $input = [
                    'item_key' => AmConfig::LANG_SETTINGS,
                    'item_tag' => AmConfig::LANGUAGE,
                    'item_value' => json_encode($oldLangSettingArr)
                ];
                AmModel::where('item_key', AmConfig::LANG_SETTINGS)->update($input);
            }

        } else {
            $input = [
                'item_key' => AmConfig::LANG_SETTINGS,
                'item_tag' => AmConfig::LANGUAGE,
                'item_value' => json_encode([$data])
            ];
            AmModel::insert($input);

            $languageStatus = AmModel::where('item_key', AmConfig::DEFAULT_LANGUAGE)->first();
            $data['alias_key'] = AmConfig::DEFAULT_LANGUAGE;
            $data['item_value'] = $data['key'];
            $data['item_type'] = AmConfig::LANGUAGE;
            if ($languageStatus) {
                if ($data['key']) {
                    $input = [
                        'item_value' => json_encode($data)
                    ];
                    AmModel::where('item_key', AmConfig::DEFAULT_LANGUAGE)->update($input);
                }

            } else {
                $input = [
                    'item_key' => AmConfig::DEFAULT_LANGUAGE,
                    'item_tag' => AmConfig::LANGUAGE,
                    'item_value' => json_encode($data)
                ];
                AmModel::insert($input);
            }

        }

        $this->success();
    }

    //编辑语言设置
    public function updateLang(Request $request)
    {
        $key = $request->input('key', null);
        $rankNum = $request->input('rankNum', null);
        $langCode = $request->input('langCode', null);
        $langName = $request->input('langName', null);
        $writingDirection = $request->input('writingDirection', null);
        $language_status = $request->input('language_status', null);
        $continents = $request->input('continents', null);
        $areaCode = $request->input('areaCode', null);
        $areaName = $request->input('areaName', null);
        $dateFormat = $request->input('dateFormat', null);
        $timeFormatMinute = $request->input('timeFormatMinute', null);
        $timeFormatHour = $request->input('timeFormatHour', null);
        $timeFormatDay = $request->input('timeFormatDay', null);
        $timeFormatMonth = $request->input('timeFormatMonth', null);
        $lengthUnits = $request->input('lengthUnits', null);


        $data = [
            'rankNum' => $rankNum,
            'langCode' => $langCode,
            'langName' => $langName,
            'langTag' => $langCode.'-'.$areaCode,
            'writingDirection' => $writingDirection,
            'language_status' => $language_status,
            'continents' => $continents,
            'areaCode' => $areaCode,
            'areaName' => $areaName,
            'lengthUnits' => $lengthUnits,
            'dateFormat' => $dateFormat,
            'timeFormatMinute' => $timeFormatMinute,
            'timeFormatHour' => $timeFormatHour,
            'timeFormatDay' => $timeFormatDay,
            'timeFormatMonth' => $timeFormatMonth,
            'key' => $key
        ];
        if (empty($data['areaCode'])) {
            $data['langTag'] = $langCode;
        }

        //查询是否有语言设置
        $langSettings = AmModel::where('item_key', AmConfig::LANG_SETTINGS)->first();
        if ($langSettings) {
            $oldLangSettingJson = $langSettings['item_value'];
            $oldLangSettingArr = json_decode($oldLangSettingJson, true);
            $isTrue = true;
            if ($oldLangSettingArr) {
                foreach ($oldLangSettingArr as $v) {
                    if ($v['langTag'] == $data['langTag']) {
                        if ($v['key'] != $key) {
                            $isTrue = false;
                        }
                    }
                }
            }

            if ($isTrue == true) {
                $itemArr = [];
                foreach ($oldLangSettingArr as $v) {
                    if ($v['key'] == $key) {
                        $v = $data;
                    }
                    $itemArr[] = $v;
                }

                $oldLangSettingArr[] = $data;
                $input = [
                    'item_key' => AmConfig::LANG_SETTINGS,
                    'item_tag' => AmConfig::LANGUAGE,
                    'item_value' => json_encode($itemArr)
                ];
                AmModel::where('item_key', AmConfig::LANG_SETTINGS)->update($input);
            }

        }

        $this->success();
    }

    //排序
    public function updateRankNum(Request $request)
    {
        $json = $request->input('more_json');
        $jsonArr = json_decode($json, true);
        $langSettings = AmModel::where('item_key', AmConfig::LANG_SETTINGS)->first();
        if ($langSettings) {
            $oldLangSettingJson = $langSettings['item_value'];
            $oldLangSettingArr = json_decode($oldLangSettingJson, true);
            $newArr = [];
            foreach ($oldLangSettingArr as $v) {
                foreach ($jsonArr as $value) {
                    if ($value['key'] == $v['key']) {
                        $v['rankNum'] = $value['rank_num'];
                    }
                }
                $newArr[] = $v;
            }
            $input = [
                'item_key' => AmConfig::LANG_SETTINGS,
                'item_tag' => AmConfig::LANGUAGE,
                'item_value' => json_encode($newArr)
            ];
            AmModel::where('item_key', AmConfig::LANG_SETTINGS)->update($input);
        }

        $this->success();
    }

    //删除
    public function destroyLang(Request $request)
    {
        $key = $request->input('ids');
        $keyArr = explode(',', $key);

        //默认
        // $defaultLanguage = AmModel::where('item_key',AmConfig::DEFAULT_LANGUAGE)->value('item_value');
        // $defaultLanguageArr = json_decode($defaultLanguage,true);
        $languageArr = FresnsConfigService::getLanguageStatus();


        $defaultLanguage = $languageArr['default_language'];
        if (in_array($defaultLanguage, $keyArr)) {
            $this->errorInfo(ErrorCodeService::CODE_FAIL, '默认语言不允许删除');
        }

        $langSettings = AmModel::where('item_key', AmConfig::LANG_SETTINGS)->first();
        $oldLangSettingJson = $langSettings['item_value'];
        $oldLangSettingArr = json_decode($oldLangSettingJson, true);
        $itemArr = [];
        $destroyArr = [];
        foreach ($oldLangSettingArr as $v) {
            if (in_array($v['key'], $keyArr)) {
                $lang = $v['langTag'];
                $destroyArr[] = $lang;
                continue;
            }
            $itemArr[] = $v;
        }

        TweetLanguages::whereIn('lang_code', $destroyArr)->delete();

        $input = [
            'item_key' => AmConfig::LANG_SETTINGS,
            'item_tag' => AmConfig::LANGUAGE,
            'item_value' => json_encode($itemArr)
        ];
        AmModel::where('item_key', AmConfig::LANG_SETTINGS)->update($input);

        $this->success();
    }

    //是否开启多语言
    public function updateLanguageStatus(Request $request)
    {
        $language_status = $request->input('language_status');
        $languageStatus = AmModel::where('item_key', AmConfig::LANGUAGE_STATUS)->first();
        $data['alias_key'] = AmConfig::LANGUAGE_STATUS;
        $data['item_value'] = $language_status;
        $data['item_type'] = AmConfig::LANGUAGE;
        if ($languageStatus) {
            $input = [
                'item_value' => $language_status
            ];
            AmModel::where('item_key', AmConfig::LANGUAGE_STATUS)->update($input);
        } else {
            $input = [
                'item_key' => AmConfig::LANGUAGE_STATUS,
                'item_tag' => AmConfig::LANGUAGE,
                'item_value' => $language_status
            ];
            AmModel::insert($input);
        }

        $this->success();
    }

    //默认语言
    public function updateDefaultLanguage(Request $request)
    {
        $key = $request->input('key');
        $defaultKey = ApiLanguageHelper::getDefaultLanguageByKey($key);
        $languageStatus = AmModel::where('item_key', AmConfig::DEFAULT_LANGUAGE)->first();
        $data['alias_key'] = AmConfig::DEFAULT_LANGUAGE;
        $data['item_value'] = $key;
        $data['item_type'] = AmConfig::LANGUAGE;
        if ($languageStatus) {
            if ($key) {
                $input = [
                    'item_value' => $defaultKey
                ];
                AmModel::where('item_key', AmConfig::DEFAULT_LANGUAGE)->update($input);
            }

        } else {
            $input = [
                'item_key' => AmConfig::DEFAULT_LANGUAGE,
                'item_tag' => AmConfig::LANGUAGE,
                'item_value' => $defaultKey
            ];
            AmModel::insert($input);
        }

        $this->success();
    }

    //语言包列表
    public function packIndex(Request $request)
    {
        // 校验参数
        $rule = [
            'lang_tag' => 'required',
        ];
        ValidateService::validateRule($request, $rule);
        $lang_tag = $request->input('lang_tag');
        $langTagJson = AmModel::where('item_key', $lang_tag)->value('item_value');
        $langTagArr = json_decode($langTagJson, true);
        $langTagMap = [];
        if ($langTagArr) {
            foreach ($langTagArr as $v) {
                $langTagMap[$v['name']] = $v['content'];
            }
        }

        $packJson = AmModel::where('item_key', 'language_pack')->value('item_value');
        $packArr = json_decode($packJson, true);

        //查询默认是否有
        $defaultLanguage = ApiLanguageHelper::getDefaultLanguage();

        $defaultLangTagJson = AmModel::where('item_key', $defaultLanguage)->value('item_value');
        $defaultLangTagArr = json_decode($defaultLangTagJson, true);
        $defaultLangTagArrMap = [];
        if ($defaultLangTagArr) {
            foreach ($defaultLangTagArr as $v) {
                $defaultLangTagArrMap[$v['name']] = $v['content'];
            }
        }

        $list = [];
        foreach ($packArr as $v) {
            $item = [];
            $item['key'] = $v['name'];
            $item['name'] = $v['name'];
            $item['is_update'] = $v['canDelete'];
            $item['default_content'] = $defaultLangTagArrMap[$v['name']] ?? '';
            $item['content'] = $langTagMap[$v['name']] ?? '';
            $list[] = $item;
        }


        $data['list'] = $list;
        // common 数据
        $data['common'] = $this->service->common();

        //分页
        $data['pagination']['total'] = count($list);
        $data['pagination']['current'] = 1;
        $data['pagination']['pageSize'] = 500;


        $this->success($data);
    }

   

    public function packUpdate(Request $request)
    {
        // 校验参数
        $rule = [
            'key' => 'required',
            'lang_tag' => 'required',
        ];
        ValidateService::validateRule($request, $rule);

        $key = $request->input('key');
        $lang_tag = $request->input('lang_tag');
        $name = $request->input('name');
        $content = $request->input('content');

        $itemValue = AmModel::where('item_key', $lang_tag)->value('item_value');
        $langTagArr = json_decode($itemValue, true);

        $itemArr = [];
        foreach ($langTagArr as $v) {
            if ($v['name'] == $key) {
                // $v['name'] = $name;
                $v['content'] = $content;
            }
            $itemArr[] = $v;
        }

        AmModel::where('item_key', $lang_tag)->update(['item_value' => json_encode($itemArr)]);

        $this->success();
    }


    //地图设置-列表
    public function mapIndex(Request $request)
    {
        $service = new AmService();

        $request->offsetSet('item_tag', AmConfig::MAP);
        $request->offsetSet('item_key_no', 'maps');
        $data = $service->searchData();
        $this->success($data);
    }

    

    //地图设置-编辑
    public function mapUpdate(Request $request)
    {
        $id = $request->input('id');
        $is_enable = $request->input('is_enable');

        AmModel::where('id', $id)->update(['is_enable' => $is_enable]);

        $this->success();
    }

    // 验证规则
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
