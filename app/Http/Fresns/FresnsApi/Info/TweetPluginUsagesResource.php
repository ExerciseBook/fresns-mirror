<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Info;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesConfig;
use App\Http\Fresns\FresnsPluginBadges\FresnsPluginBadges;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;

class TweetPluginUsagesResource extends BaseAdminResource
{

    public function toArray($request)
    {
        // form 字段
        $formMap = FresnsPluginUsagesConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $langTag = request()->header('langTag', "");
        // 语言
        $name = ApiLanguageHelper::getLanguages(FresnsPluginUsagesConfig::CFG_TABLE, 'name', $this->id);
        $type = $this->type;
        $plugin = $this->plugin_unikey;
        // $name = $name['lang_content'];
        $pluginInfo = FresnsPlugin::where('unikey', $plugin)->first();
        // $icon = $this->icon_file_url;
        $icon = ApiFileHelper::getImageSignUrlByFileIdUrl($this->icon_file_id, $this->icon_file_url);

        $url = "";
        if ($pluginInfo) {
            // $url = $pluginInfo['access_path'] .'/'. $this->parameter;
            $url = ApiFileHelper::getPluginUsagesUrl($plugin, $this->id);
        }
        $number = $this->editor_number;
        $badgesType = "";
        $badgesValue = "";
        $pluginbades = FresnsPluginBadges::where('plugin_unikey', $this->plugin_unikey)->first();
        if ($pluginbades) {
            $badgesType = $pluginbades['display_type'];
            $badgesValue = $pluginbades['value_text'] ?? $pluginbades['value_number'];
        }
        $sortNumber = [];
        if ($this->type == 4) {
            $sort_number = json_decode($this->data_sources, true);
            // dd($sort_number);
            $sortNumber = [];
            if ($sort_number) {
                foreach ($sort_number as $k => &$s) {
                    // $sArr = [];
                    foreach ($s as &$v) {

                        $introArr = [];
                        foreach ($v['intro'] as $i) {
                            $map[$i['lang_code']] = $i;
                            if ($i['lang_code'] == $langTag) {
                                $introArr['title'] = $i['title'];
                                $introArr['description'] = $i['description'];
                                $introArr['langTag'] = $i['lang_code'];
                            }
                        }
                        $v['intro'] = $introArr;
                        // $sArr[] = $item1;

                    }
                    // $arr1[$k] = $sArr;
                }
                $sortNumber = $sort_number;
            }
        }

        // $sort_number
        // dd($sort_number);
        // 默认字段
        $default = [
            'type' => $type,
            'plugin' => $plugin,
            'name' => $name == null ? "" : $name['lang_content'],
            'icon' => $icon == null ? "" : $icon,
            'url' => $url,
            'number' => $number,
            'badgesType' => $badgesType,
            'badgesValue' => $badgesValue,
            'sortNumber' => $sortNumber,
        ];

        // 合并
        $arr = $default;

        return $arr;
    }
}

