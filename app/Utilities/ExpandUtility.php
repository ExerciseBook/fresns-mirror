<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Helpers\StrHelper;
use App\Models\PluginBadge;
use App\Models\PluginUsage;
use App\Models\Icon;
use App\Models\IconLinked;
use App\Models\Tip;
use App\Models\TipLinked;
use App\Models\Extend;
use App\Models\ExtendLinked;

class ExpandUtility
{
    public static function getPluginExpands(int $type, ?int $groupId = null, ?int $scene = null, ?int $userId = null, ?string $langTag = '')
    {
        $langTag = $langTag ?: ConfigHelper::fresnsConfigByItemKey('default_language');

        if ($type == 6) {
            $expandArr = PluginUsage::where('type', 6)->where('group_id', $groupId)->get();
        } else {
            $expandArr = PluginUsage::where('type', $type)
            ->when($scene, function ($query, $scene) {
                $query->where('scene', 'like', "%$scene%");
            })
            ->get();
        }

        $expandList = [];
        foreach ($expandArr as $expand) {
            if ($expand->is_group_admin == 1) {
                $adminCheck = false;

                if ($userId && $groupId) {
                    $adminCheck = ValidationUtility::checkUserGroupAdmin($userId, $groupId);
                }

                if ($adminCheck) {
                    $expandList[] = self::getExpandItemById($expand->id, $userId, $langTag);
                }
            } else {
                $permCheck = false;

                if ($userId) {
                    $roleArr = $expand->roles ? StrHelper::commaStringToArray($expand->roles) : [];
                    $permCheck = ValidationUtility::checkUserRolePerm($userId, $roleArr);
                }

                if (empty($expand->roles)) {
                    $expandList[] = self::getExpandItemById($expand->id, $userId, $langTag);
                } elseif ($permCheck) {
                    $expandList[] = self::getExpandItemById($expand->id, $userId, $langTag);
                }
            }
        }

        return $expandList;
    }

    // get expand by id
    public static function getExpandItemById(int $usageId, int $userId = 0, string $langTag)
    {
        $usage = PluginUsage::where('id', $usageId)->first();
        $badge = PluginBadge::where('plugin_unikey', $usage['plugin_unikey'])->where('user_id', $userId)->first();

        $expand['plugin'] = $usage['plugin_unikey'];
        $expand['name'] = LanguageHelper::fresnsLanguageByTableId('plugin_usages', 'name', $usage['id'], $langTag);
        $expand['icon'] = FileHelper::fresnsFileImageUrlByColumn($usage['icon_file_id'], $usage['icon_file_url'], 'imageConfigUrl') ?? null;
        $expand['url'] = PluginHelper::fresnsPluginUsageUrl($usage['plugin_unikey'], $usage['id']);
        $expand['badgesType'] = $badge['display_type'] ?? null;
        $expand['badgesValue'] = match ($expand['badgesType']) {
            default => null,
            1 => $badge['value_number'],
            2 => $badge['value_text'],
        };
        $expand['editorNumber'] = $usage['editor_number'];
        $postByAll = self::getRankNumber('postByAll', $usage['data_sources'], $langTag);
        $postByFollow = self::getRankNumber('postByFollow', $usage['data_sources'], $langTag);
        $postByNearby = self::getRankNumber('postByNearby', $usage['data_sources'], $langTag);
        $rankNumber = array_merge($postByAll, $postByFollow, $postByNearby);
        $expand['rankNumber'] = $rankNumber;

        return $expand;
    }

    // get expand list by ids
    public static function getExpandItemListByIds(array $usageIds, int $userId = 0, string $langTag)
    {
        $expandList = [];
        foreach ($usageIds as $id) {
            $expandList[] = self::getExpandItemById($id, $userId, $langTag);
        }

        return $expandList;
    }

    public static function getRankNumber(string $key, array $dataSources, string $langTag)
    {
        $rankNumberArr = $dataSources[$key]['rankNumber'];

        $rankNumber = [];
        foreach ($rankNumberArr as $arr) {
            $item['id'] = $arr['id'];
            $item['title'] = collect($arr['intro'])->where('langTag', $langTag)->first()['title'] ?? null;
            $item['description'] = collect($arr['intro'])->where('langTag', $langTag)->first()['description'] ?? null;
            $rankNumber[] = $item;
        }

        return $rankNumber;
    }

    // get icons
    public static function getIcons(int $type, int $id, string $langTag = '')
    {
        $iconLinkedArr = IconLinked::where('linked_type', $type)->where('linked_id', $id)->get()->toArray();
        $iconArr = Icon::whereIn('id', array_column($iconLinkedArr, 'icon_id'))->where('is_enable', 1)->get();

        $iconList = [];
        foreach ($iconArr as $icon) {
            foreach ($iconLinkedArr as $iconLinked) {
                if ($iconLinked['icon_id'] !== $icon['id']) {
                    continue;
                }
                $item['code'] = $iconLinked['icon_code'];
                $item['name'] = LanguageHelper::fresnsLanguageByTableId('icons', 'name', $icon['id'], $langTag);
                $item['icon'] = FileHelper::fresnsFileImageUrlByColumn($icon['icon_file_id'], $icon['icon_file_url'], 'imageConfigUrl');
                $item['iconActive'] = FileHelper::fresnsFileImageUrlByColumn($icon['active_icon_file_id'], $icon['active_icon_file_url'], 'imageConfigUrl');
                $item['type'] = $icon['type'];
                $item['url'] = ! empty($icon['plugin_unikey']) ? PluginHelper::fresnsPluginUrlByUnikey($icon['plugin_unikey']) : null;
            }

            $iconList[] = $item;
        }

        return $iconList;
    }

    // get tips
    public static function getTips(int $type, int $id, string $langTag = '')
    {
        $tipLinkedArr = TipLinked::where('linked_type', $type)->where('linked_id', $id)->get()->toArray();
        $tipArr = Tip::whereIn('id', array_column($tipLinkedArr, 'tip_id'))->where('is_enable', 1)->get();

        $tipList = [];
        foreach ($tipArr as $tip) {
            $item['icon'] = FileHelper::fresnsFileImageUrlByColumn($tip['icon_file_id'], $tip['icon_file_url'], 'imageConfigUrl');
            $item['content'] = LanguageHelper::fresnsLanguageByTableId('tips', 'content', $tip->id, $langTag);
            $item['style'] = $tip->style;
            $item['type'] = $tip->type;
            $item['url'] = ! empty($tip->plugin_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($tip->plugin_unikey) : null;

            $tipList[] = $item;
        }

        return $tipList;
    }

    // get extends
    public static function getExtends(int $type, int $id, string $langTag = '')
    {
        $extendLinkedArr = ExtendLinked::where('linked_type', $type)->where('linked_id', $id)->get()->toArray();
        $extendArr = Extend::whereIn('id', array_column($extendLinkedArr, 'extend_id'))->where('is_enable', 1)->get();

        $extendList = [];
        foreach ($extendArr as $extend) {
            $item['eid'] = $extend->eid;
            $item['frameType'] = $extend->frame_type;
            $item['framePosition'] = $extend->frame_position;
            $item['textContent'] = $extend->text_content;
            $item['textIsMarkdown'] = $extend->text_is_markdown;
            $item['cover'] = FileHelper::fresnsFileImageUrlByColumn($extend['cover_file_id'], $extend['cover_file_url'], 'imageConfigUrl');
            $item['title'] = LanguageHelper::fresnsLanguageByTableId('extends', 'title', $extend->id, $langTag);
            $item['titleColor'] = $extend->title_color;
            $item['descPrimary'] = LanguageHelper::fresnsLanguageByTableId('extends', 'desc_primary', $extend->id, $langTag);
            $item['descPrimaryColor'] = $extend->desc_primary_color;
            $item['descSecondary'] = LanguageHelper::fresnsLanguageByTableId('extends', 'desc_secondary', $extend->id, $langTag);
            $item['descSecondaryColor'] = $extend->desc_secondary_color;
            $item['btnName'] = LanguageHelper::fresnsLanguageByTableId('extends', 'btn_name', $extend->id, $langTag);
            $item['type'] = $extend->extend_type;
            $item['target'] = $extend->extend_target;
            $item['value'] = $extend->extend_value;
            $item['support'] = $extend->extend_support;
            $item['moreJson'] = $extend->more_json;

            $extendList[] = $item;
        }

        return $extendList;
    }
}
