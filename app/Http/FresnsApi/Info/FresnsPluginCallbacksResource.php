<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Info;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsExtends\FresnsExtendsConfig;
use App\Http\FresnsDb\FresnsPluginCallbacks\FresnsPluginCallbacks;
use App\Http\FresnsDb\FresnsPluginCallbacks\FresnsPluginCallbacksConfig;

/**
 * List resource config handle.
 */
class FresnsPluginCallbacksResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = FresnsPluginCallbacksConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // Insert unikey
        $unikey = $request->input('unikey');
        FresnsPluginCallbacks::where('id', $this->id)->update(['use_plugin_unikey' => $unikey, 'status' => 2]);
        $content = json_decode($this->content, true);
        if ($content) {
            foreach ($content as &$t) {
                // callbackType=4
                // Handle file anti hotlinking URL
                if ($t['callbackType'] == 4) {
                    $files = $t['dataValue'];
                    if ($files) {
                        $arr = ApiFileHelper::getMoreJsonSignUrl($files);
                    }
                    $t['dataValue'] = $arr;
                }
            }
            // callbackType=9
            // Get all extended content data
            if ($t['callbackType'] == 9) {
                $extendsArr = $t['dataValue'];
                $extends = [];
                if ($extendsArr) {
                    foreach ($extendsArr as $e) {
                        $arr = [];
                        $extendsInfo = FresnsExtends::where('uuid', $e['eid'])->first();
                        if ($extendsInfo) {
                            $arr['eid'] = $e['eid'];
                            $arr['canDelete'] = $e['canDelete'] ?? 'true';
                            $arr['rankNum'] = $e['rankNum'] ?? 9;
                            $arr['plugin'] = $extendsInfo['plugin_unikey'] ?? '';
                            $arr['frame'] = $extendsInfo['frame'] ?? '';
                            $arr['position'] = $extendsInfo['position'] ?? '';
                            $arr['content'] = $extendsInfo['text_content'] ?? '';
                            if ($extendsInfo['frame'] == 1) {
                                $arr['files'] = json_decode($extendsInfo['text_files'], true);
                                if ($arr['files']) {
                                    $arr['files'] = ApiFileHelper::getMoreJsonSignUrl($arr['files']);
                                }
                            }
                            $arr['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($extendsInfo['cover_file_id'], $extendsInfo['cover_file_url']);
                            $title = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'title', $extendsInfo['id']);
                            $title = $title == null ? '' : $title['lang_content'];
                            $arr['title'] = $title;
                            $arr['titleColor'] = $extendsInfo['title_color'] ?? '';
                            $descPrimary = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'desc_primary', $extendsInfo['id']);
                            $descPrimary = $descPrimary == null ? '' : $descPrimary['lang_content'];
                            $arr['descPrimary'] = $descPrimary;
                            $arr['descPrimaryColor'] = $extendsInfo['desc_primary_color'] ?? '';
                            $descSecondary = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'desc_secondary', $extendsInfo['id']);
                            $descSecondary = $descSecondary == null ? '' : $descSecondary['lang_content'];
                            $arr['descSecondary'] = $descSecondary;
                            $arr['descSecondaryColor'] = $extendsInfo['desc_secondary_color'] ?? '';
                            $btnName = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'btn_name', $extendsInfo['id']);
                            $btnName = $btnName == null ? '' : $btnName['lang_content'];
                            $arr['btnName'] = $btnName;
                            $arr['btnColor'] = $extendsInfo['btn_color'] ?? '';
                            $arr['type'] = $extendsInfo['extend_type'] ?? '';
                            $arr['target'] = $extendsInfo['extend_target'] ?? '';
                            $arr['value'] = $extendsInfo['extend_value'] ?? '';
                            $arr['support'] = $extendsInfo['extend_support'] ?? '';
                            $arr['moreJson'] = ApiFileHelper::getMoreJsonSignUrl($extendsInfo['moreJson']) ?? [];
                            $extends[] = $arr;
                        }
                    }
                    $t['dataValue'] = $extends;
                }
            }
        }

        return $content;
    }
}
