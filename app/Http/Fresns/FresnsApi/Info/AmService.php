<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Info;

use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsExtends\FresnsExtends;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesConfig;
use App\Http\Fresns\FresnsLanguages\FresnsLanguages;

class AmService
{
    public static function getlanguageField($field, $id)
    {
        if (!$id) {
            return "";
        }
        $langTag = ApiLanguageHelper::getLangTagByHeader();
         
        $input = [
            'lang_tag' => $langTag,
            'table_field' => $field,
            'table_id' => $id,
            'table_name' => FresnsPluginUsagesConfig::CFG_TABLE,
        ];
        $name = FresnsLanguages::where($input)->first();
        if (!$name) {
            $input = [
                'table_field' => $field,
                'table_id' => $id,
                'table_name' => FresnsPluginUsagesConfig::CFG_TABLE,
            ];
            $name = FresnsLanguages::where($input)->first();
        }
        return $name;
    }

    //获取查询到和自己关注的信息
    public static function getMemberFollows($queryType, $idArr, $mid, $langTag = null)
    {
        $data = [];
        switch ($queryType) {
            case 1:
                //查询关联表
                $followIdArr = FresnsMemberFollows::where('member_id', $mid)
                    ->where('follow_type', FresnsMemberFollowsConfig::FOLLOW_TYPE_1)
                    ->pluck('follow_id')
                    ->whereIn('follow_id', $idArr)
                    ->toArray();
                break;
            case 2:
                //查询关联表
                $followIdArr = FresnsMemberFollows::where('member_id', $mid)
                    ->where('follow_type', FresnsMemberFollowsConfig::FOLLOW_TYPE_2)
                    ->pluck('follow_id')
                    ->whereIn('follow_id', $idArr)
                    ->toArray();
                break;
            case 3:
                //查询关联表
                $followIdArr = FresnsMemberFollows::where('member_id', $mid)
                    ->where('follow_type', FresnsMemberFollowsConfig::FOLLOW_TYPE_3)
                    ->pluck('follow_id')
                    ->whereIn('follow_id', $idArr)
                    ->toArray();

                break;
            case 4:
                //查询关联表
                $followIdArr = FresnsMemberFollows::where('member_id', $mid)
                    ->where('follow_type', FresnsMemberFollowsConfig::FOLLOW_TYPE_4)
                    ->whereIn('follow_id', $idArr)
                    ->pluck('follow_id')
                    ->toArray();
                break;
            case 5:
                $followIdArr = FresnsExtends::whereIn('id', $idArr)->where('member_id', $mid)->pluck('id')->toArray();

                break;
            default:
                $followIdArr = [];
                break;
        }

        if ($followIdArr) {
            //每次输出数量
            $count = FresnsMemberFollowsConfig::INPUTTIPS_COUNT;
            $followCount = count($followIdArr);

            if ($followCount == $count) {
                $data = $followIdArr;
            }
            if ($followCount > $count) {
                $data = array_slice($followIdArr, 0, $count);
            }
            if ($followCount < $count) {

                //求数组的差集
                $diffArr = array_diff($idArr, $followIdArr);
                $diffCount = $count - $followCount;
                sort($diffArr);
                $diffArr = array_slice($diffArr, 0, $diffCount);

                $data = array_merge($followIdArr, $diffArr);
            }
        } else {
            $data = $idArr;
        }

        return $data;
    }
}
