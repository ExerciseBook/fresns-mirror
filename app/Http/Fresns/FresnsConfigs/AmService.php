<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsConfigs;

use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsLanguages\FresnsLanguages;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsPlugin\FresnsPluginConfig;
use Illuminate\Support\Facades\DB;

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

        $common['sitePrivatePluginOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_MODE);
        $common['sitePrivateEndOption'] = AmConfig::SITE_PRIVATE_END_OPTION;
        $common['siteOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_SITE);
        $common['emailOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_EMAIL);
        $common['smsOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_SMS);
        $common['phoneOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_PHONE);
        $common['wechatOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_WECHAT);
        $common['imageOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_IMAGE);
        $common['videoOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_VIDEO);
        $common['audioOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_AUDIO);
        $common['fileOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_FILE);
        $common['registerOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_REGISTER);
        $common['verificationOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_VERIFICATION);
        $common['mapOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::PLUGINS_MAP);
        $common['manyUsersOption'] = FresnsPlugin::buildSelectOptionsByUnikey(FresnsPluginConfig::MANY_USERS);
        //获取对应的七大洲编号
        $continents = self::getItemValueArrByItemKey(AmConfig::CONTINENTS);
        $continentsArr = [];
        foreach ($continents as $v) {
            $item = [];
            $item['key'] = $v['id'];
            $item['text'] = $v['name'];
            $continentsArr[] = $item;
        }
        //获取地图服务商
        $mapArr = self::getItemValueArrByItemKey('maps');
        $commonMapArr = [];
        foreach ($mapArr as $v) {
            $item = [];
            $item['key'] = $v['id'];
            $item['text'] = $v['name'];
            $commonMapArr[] = $item;
        }
        $common['mapServiceOption'] = $commonMapArr;
        $common['continentOption'] = $continentsArr;
        //获取语言代码
        $languageArr = self::getItemValueArrByItemKey(AmConfig::LANGUAGE_CODES);
        foreach ($languageArr as &$language) {
            $language['name_desc'] = $language['name'].' - '.$language['localName'].' > '.$language['code'];
        }
        $common['languageOption'] = $languageArr;
        //获取地区代码
        $areasArr = self::getItemValueArrByItemKey(AmConfig::AREAS);

        foreach ($areasArr as &$area) {
            $area['name_desc'] = $area['name'].' - '.$area['localName'].' > '.$area['code'];
        }
        $common['areasOption'] = $areasArr;
        $common['dateOption'] = AmConfig::DATE_OPTION;
        $common['lengthUnitsOption'] = AmConfig::LENGTHUNITS_OPTION;

        $common['memberRoleOption'] = FresnsMemberRoles::staticBuildSelectOptions();
        $languageArr = FresnsConfigService::getLanguageStatus();
        $common['language_status'] = $languageArr['language_status'];
        $common['default_language'] = $languageArr['default_language'];
        $common['languagesOption'] = $languageArr['languagesOption'];
        $common['defaultTimezoneOption'] = AmConfig::DEFAULT_TIMEZONE_OPTION;
        //钱包货币
        $common['walletCurrencyCodeOption'] = AmConfig::WALLET_CURRENCY_CODE_OPTION;

        return $common;
    }

    //获取对应的地区编号
    public static function getItemValueArrByItemKey($itemKey)
    {
        $configs = AmModel::where('item_key', $itemKey)->value('item_value');

        $content = json_decode($configs, true);
        return $content;
    }

    public function updateConfigs($configArr)
    {
        $addArr = [];
        $updateArr = [];
        foreach ($configArr as $v) {
            $itemKeyMap = AmModel::where('item_tag', $v['alias_key'])->pluck('id', 'item_key')->toArray();
            foreach ($v['dict_item_arr'] as $i) {
                $item = [];
                if (!empty($i['lang_json'])) {
                    DB::table('languages')->where('table_name', FresnsConfigsConfig::CFG_TABLE)->where('table_key',
                        $i['alias_key'])->delete();
                    $langArr = json_decode($i['lang_json'], true);
                    $itemArr = [];
                    foreach ($langArr as $lang) {
                        $item = [];
                        $item['table_name'] = FresnsConfigsConfig::CFG_TABLE;
                        $item['table_field'] = 'item_value';
                        $item['table_key'] = $i['alias_key'];
                        $tag = FresnsLanguagesService::conversionLangTag($lang['key']);
                        $langCode = $tag['lang_code'];
                        $areaCode = $tag['area_code'];
                        $item['lang_code'] = $langCode;
                        $item['area_code'] = $areaCode ?? null;
                        $item['lang_tag'] = $lang['key'];
                        $item['lang_content'] = $lang['lang_content'] ?? null;
                        $itemArr[] = $item;
                    }

                    FresnsLanguages::insert($itemArr);
                }
                if (!empty($itemKeyMap[$i['alias_key']])) {
                    $item = [];
                    $item['id'] = $itemKeyMap[$i['alias_key']];
                    $value = $i['item_value'] ?? null;
                    if ($i['item_type'] == 'checkbox' || $i['item_type'] == 'select') {
                        if (!empty($i['item_value'])) {
                            if (is_array($i['item_value'])) {
                                $value = implode(',', $i['item_value']);
                            } else {
                                $value = $i['item_value'];
                            }
                        }
                    }

                    if (is_bool($value)) {
                        if ($value == true) {
                            $value = 'true';
                        } else {
                            $value = 'false';
                        }
                        // if($i['alias_key'] = 'site_close'){
                        //     dd($value)
                        // }
                    }
                    $item['item_value'] = $value ?? null;
                    $item['item_tag'] = $v['alias_key'];
                    $item['item_type'] = $i['item_type'];
                    $updateArr[] = $item;
                } else {
                    $value = $i['item_value'] ?? null;

                    if ($i['item_type'] == 'checkbox' || $i['item_type'] == 'select') {
                        if (!empty($i['item_value'])) {
                            if (is_array($i['item_value'])) {
                                $value = implode(',', $i['item_value']);
                            } else {
                                $value = $i['item_value'];
                            }
                        }
                    }
                    if (is_bool($value)) {
                        if ($value == true) {
                            $value = 'true';
                        } else {
                            $value = 'false';
                        }
                    }
                    $item['item_key'] = $i['alias_key'];
                    $item['item_value'] = $value;
                    $item['item_tag'] = $v['alias_key'];
                    $item['item_type'] = $i['item_type'];
                    $addArr[] = $item;
                }
            }

        }

        $model = new AmModel();

        if ($addArr) {
            AmModel::insert($addArr);
        } else {

            $model->updateBatch($updateArr);
        }
    }

    public function packStore($langTag, $langTagArr, $packArr)
    {
        foreach ($packArr as $v) {
            $langTagNameArr[] = $v['name'];
        }

        $nameArr = [];
        foreach ($langTagArr as $value) {
            if (!in_array($value['name'], $langTagNameArr)) {
                $item = [];
                $item['name'] = $value['name'];
                $item['canDelete'] = "false";
                $nameArr[] = $item;
            }
        }

        $packArr = array_merge($nameArr, $packArr);

        $packJson = json_encode($packArr);
        $count = AmModel::where('item_key', 'language_pack')->count();
        if ($count > 0) {
            AmModel::where('item_key', 'language_pack')->update(['item_value' => $packJson]);
        } else {
            $input = [
                'item_key' => 'language_pack',
                'item_value' => $packJson
            ];
            AmModel::where('item_key', 'language_pack')->insert($input);
        }

        $langTagMap = [];
        foreach ($langTagArr as $value) {
            $langTagMap[$value['name']] = $value['content'];
        }

        $addLangTagArr = [];
        foreach ($packArr as $v) {
            $item = [];
            $item['name'] = $v['name'];
            $item['content'] = $langTagMap[$v['name']] ?? null;
            $addLangTagArr[] = $item;
        }

        $count = AmModel::where('item_key', $langTag)->count();
        $addLangTagJson = json_encode($addLangTagArr);
        if ($count > 0) {
            AmModel::where('item_key', $langTag)->update(['item_value' => $addLangTagJson]);

        } else {
            $input = [
                'item_key' => $langTag,
                'item_value' => $addLangTagJson
            ];

            AmModel::insert($input);

        }

        return true;

    }

}