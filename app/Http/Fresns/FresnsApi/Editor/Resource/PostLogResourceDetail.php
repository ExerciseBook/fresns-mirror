<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Editor\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsPostLogs\FresnsPostLogsConfig;
use App\Http\Fresns\FresnsPosts\FresnsPosts;
use App\Http\Fresns\FresnsExtends\FresnsExtends;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsExtends\FresnsExtendsConfig;
class PostLogResourceDetail extends BaseAdminResource
{


    public function toArray($request)
    {
        $formMap = FresnsPostLogsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $postInfo = FresnsPosts::find($this->post_id);
        // dd(json_decode($this->editor_json,true));
        $extends_json = json_decode($this->extends_json, true);
        $extends = [];
        if($extends_json){
            foreach($extends_json as $e){
                $arr = [];
                $extendsInfo = FresnsExtends::where('uuid',$e['eid'])->first();
                if($extendsInfo){
                    $arr['eid'] = $e['eid'];
                    $arr['canDelete'] = $e['canDelete'] ?? 'true';
                    $arr['rankNum'] = $e['rankNum'] ?? 9;
                    $arr['plugin'] = $extendsInfo['plugin_unikey'] ?? "";
                    $arr['frame'] = $extendsInfo['frame'] ?? "";
                    $arr['position'] = $extendsInfo['position'] ?? "";
                    $arr['content'] = $extendsInfo['text_content'] ?? "";
                    if ($extendsInfo['frame'] == 1) {
                        $arr['files'] = json_decode($extendsInfo['text_files'],true);
                        if($arr['files']){
                           $arr['files'] = ApiFileHelper::getMoreJsonSignUrl($arr['files']);
                        }
                    }
                    // $arr['cover'] = $extendsInfo['cover_file_url'] ?? "";
                    // if($arr['cover']){
                    $arr['cover'] =  ApiFileHelper::getImageSignUrlByFileIdUrl($extendsInfo['cover_file_id'], $extendsInfo['cover_file_url']);
                    // }
                    $title = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'title', $extendsInfo['id']);
                    $title = $title == null ? "" : $title['lang_content'];
                    $arr['title'] = $title;
                    $arr['titleColor'] = $extendsInfo['title_color'] ?? "";
                    $descPrimary = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'desc_primary',$extendsInfo['id']);
                    $descPrimary = $descPrimary == null ? "" : $descPrimary['lang_content'];
                    $arr['descPrimary'] = $descPrimary;
                    $arr['descPrimaryColor'] = $extendsInfo['desc_primary_color'] ?? "";
                    $descSecondary = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE,'desc_secondary', $extendsInfo['id']);
                    $descSecondary = $descSecondary == null ? "" : $descSecondary['lang_content'];
                    $arr['descSecondary'] = $descSecondary;
                    $arr['descSecondaryColor'] = $extendsInfo['desc_secondary_color'] ?? "";
                    $btnName = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'btn_name',$extendsInfo['id']);
                    $btnName = $btnName == null ? "" : $btnName['lang_content'];
                    $arr['btnName'] = $btnName;
                    $arr['btnColor'] = $extendsInfo['btn_color'] ?? "";
                    $arr['type'] = $extendsInfo['extend_type'] ?? "";
                    $arr['target'] = $extendsInfo['extend_target'] ?? "";
                    $arr['value'] = $extendsInfo['extend_value'] ?? "";
                    $arr['support'] = $extendsInfo['extend_support'] ?? "";
                    $arr['moreJson'] = ApiFileHelper::getMoreJsonSignUrl($extendsInfo['moreJson'] ) ?? [];
                    $extends[] = $arr;
                }
            }
        }
        $files_decode = json_decode($this->files_json,true);
        $files = [];
        // dump($files_decode);
        if($files_decode){
            $files = ApiFileHelper::getMoreJsonSignUrl($files_decode);
        }

        $default = [
            'id' => $this->id,
            'pid' => $postInfo['uuid'] ?? "",
            'gid' => $this->group_id,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'isMarkdown' => $this->is_markdown,
            'isAnonymous' => $this->is_anonymous,
            'editor' => json_decode($this->editor_json, true) ?? [],
            'allow' => json_decode($this->allow_json, true) ?? [],
            'commentSetting' => json_decode($this->comment_set_json, true) ?? [],
            'location' => json_decode($this->location_json, true) ?? [],
            'files' => $files,
            'extends' => $extends,
        ];
        return $default;
    }
}