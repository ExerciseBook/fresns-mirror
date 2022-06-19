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
use App\Models\ArchiveUse;
use App\Models\Extend;
use App\Models\ExtendUse;
use App\Models\Operation;
use App\Models\OperationUse;
use App\Models\Plugin;
use App\Models\PluginUse;

class ExtendUtility
{
    // get plugin uses
    public static function getPluginExtends(int $type, ?int $groupId = null, ?int $scene = null, ?int $userId = null, ?string $langTag = null)
    {
        $langTag = $langTag ?: ConfigHelper::fresnsConfigDefaultLangTag();

        if ($type == 6) {
            $extendArr = PluginUse::where('type', $type)->where('group_id', $groupId)->orderBy('rating')->get();
        } else {
            $extendArr = PluginUse::where('type', $type)->when($scene, function ($query, $scene) {
                $query->where('scene', 'like', "%$scene%");
            })->orderBy('rating')->get();
        }

        $extendList = null;
        foreach ($extendArr as $extend) {
            if ($extend->is_group_admin == 1) {
                if (! empty($userId) && ! empty($groupId)) {
                    $adminCheck = PermissionUtility::checkUserGroupAdmin($groupId, $userId);
                } else {
                    $adminCheck = false;
                }

                if ($adminCheck) {
                    $extendList[] = $extend->getUseInfo($langTag, $userId);
                }
            } else {
                if (! empty($userId) && ! empty($extend->roles)) {
                    $roleArr = explode(',', $extend->roles);
                    $permCheck = PermissionUtility::checkUserRolePerm($userId, $roleArr);
                } else {
                    $permCheck = false;
                }

                if (empty($extend->roles) || $permCheck) {
                    $extendList[] = $extend->getUseInfo($langTag, $userId);
                }
            }
        }

        return $extendList;
    }

    // get data extend
    public static function getDataExtend(string $contentType, string $dataType)
    {
        $dataConfig = PluginUse::type(PluginUse::TYPE_CONTENT)->where('plugin_unikey', $contentType)->isEnable()->value('data_sources');

        if (empty($dataConfig)) {
            return null;
        }

        $dataPluginUnikey = $dataConfig[$dataType]['pluginUnikey'] ?? null;

        $dataPlugin = Plugin::where('unikey', $dataPluginUnikey)->isEnable()->first();

        if (empty($dataPlugin)) {
            return null;
        }

        return $dataPlugin->unikey;
    }

    // get operations
    public static function getOperations(int $type, int $id, ?string $langTag = null)
    {
        $operationQuery = OperationUse::with('operation')->type($type)->where('use_id', $id);

        $operationQuery->whereHas('operation', function ($query) {
            $query->where('is_enable', 1);
        });

        $operations = $operationQuery->get()->map(function ($operationUse) use ($langTag) {
            $item['type'] = $operationUse->operation->type;
            $item['code'] = $operationUse->operation->code;
            $item['style'] = $operationUse->operation->style;
            $item['title'] = LanguageHelper::fresnsLanguageByTableId('operations', 'title', $operationUse->operation->id, $langTag);
            $item['description'] = LanguageHelper::fresnsLanguageByTableId('operations', 'description', $operationUse->operation->id, $langTag);
            $item['imageUrl'] = FileHelper::fresnsFileUrlByTableColumn($operationUse->operation->image_file_id, $operationUse->operation->image_file_url);
            $item['imageActiveUrl'] = FileHelper::fresnsFileUrlByTableColumn($operationUse->operation->image_active_file_id, $operationUse->operation->image_active_file_url);
            $item['useType'] = $operationUse->operation->use_type;
            $item['useUrl'] = PluginHelper::fresnsPluginUrlByUnikey($operationUse->operation->plugin_unikey);

            return $item;
        })->groupBy('type');

        $operationList['customizes'] = $operations->get(Operation::TYPE_CUSTOMIZE)?->all() ?? null;
        $operationList['buttonIcons'] = $operations->get(Operation::TYPE_BUTTON_ICON)?->all() ?? null;
        $operationList['diversifyImages'] = $operations->get(Operation::TYPE_DIVERSIFY_IMAGE)?->all() ?? null;
        $operationList['tips'] = $operations->get(Operation::TYPE_TIP)?->all() ?? null;

        return $operationList;
    }

    // get archives
    public static function getArchives(int $type, int $id, ?string $langTag = null)
    {
        $archiveQuery = ArchiveUse::with('archive')->type($type)->where('use_id', $id)->isEnable();

        $archiveQuery->whereHas('archive', function ($query) {
            $query->where('is_enable', 1)->orderBy('rating');
        });

        $archiveUses = $archiveQuery->get();

        $archiveList = null;
        foreach ($archiveUses as $use) {
            $archive = $use->archive;

            $item['code'] = $archive->code;
            $item['name'] = LanguageHelper::fresnsLanguageByTableId('archives', 'name', $archive->id, $langTag);

            if ($archive->api_type == 'file' && is_int($use->archive_value)) {
                $item['value'] = ConfigHelper::fresnsConfigFileUrlByItemKey($use->archive_value);
            } elseif ($archive->api_type == 'plugin') {
                $item['value'] = PluginHelper::fresnsPluginUrlByUnikey($use->archive_value);
            } elseif ($archive->api_type == 'plugins') {
                if ($use->archive_value) {
                    foreach ($use->archive_value as $plugin) {
                        $plugin['code'] = $plugin['code'];
                        $plugin['url'] = PluginHelper::fresnsPluginUrlByUnikey($plugin['unikey']);
                        $pluginArr[] = $plugin;
                    }
                    $item['value'] = $pluginArr;
                }
            } else {
                $item['value'] = $use->archive_value;
            }

            $archiveList[] = $item;
        }

        return $archiveList;
    }

    // get extends
    public static function getExtends(int $type, int $id, ?string $langTag = null)
    {
        $extendQuery = ExtendUse::with('extend')->type($type)->where('use_id', $id)->orderBy('rating');

        $extendQuery->whereHas('extend', function ($query) {
            $query->where('is_enable', 1);
        });

        $extends = $extendQuery->get()->map(function ($extendLinked) use ($langTag) {
            $item['eid'] = $extendLinked->extend->eid;
            $item['type'] = $extendLinked->extend->type;
            $item['textContent'] = $extendLinked->extend->text_content;
            $item['textIsMarkdown'] = (bool) $extendLinked->extend->text_is_markdown;
            $item['infoType'] = $extendLinked->extend->info_type;
            $item['cover'] = FileHelper::fresnsFileUrlByTableColumn($extendLinked->extend->cover_file_id, $extendLinked->extend->cover_file_url);
            $item['title'] = LanguageHelper::fresnsLanguageByTableId('extends', 'title', $extendLinked->extend->id, $langTag) ?? $extendLinked->extend->title;
            $item['titleColor'] = $extendLinked->extend->title_color;
            $item['descPrimary'] = LanguageHelper::fresnsLanguageByTableId('extends', 'desc_primary', $extendLinked->extend->id, $langTag) ?? $extendLinked->extend->desc_primary;
            $item['descPrimaryColor'] = $extendLinked->extend->desc_primary_color;
            $item['descSecondary'] = LanguageHelper::fresnsLanguageByTableId('extends', 'desc_secondary', $extendLinked->extend->id, $langTag) ?? $extendLinked->extend->desc_secondary;
            $item['descSecondaryColor'] = $extendLinked->extend->desc_secondary_color;
            $item['buttonName'] = LanguageHelper::fresnsLanguageByTableId('extends', 'button_name', $extendLinked->extend->id, $langTag) ?? $extendLinked->extend->button_name;
            $item['buttonColor'] = $extendLinked->extend->button_color;
            $item['position'] = $extendLinked->extend->position;
            $item['accessUrl'] = PluginHelper::fresnsPluginUseUrl($extendLinked->extend->plugin_unikey, $extendLinked->extend->parameter);
            $item['moreJson'] = $extendLinked->extend->more_json;

            return $item;
        })->groupBy('type');

        $operationList['textBox'] = $extends->get(Extend::TYPE_TEXT_BOX)?->all() ?? null;
        $operationList['infoBox'] = $extends->get(Extend::TYPE_INFO_BOX)?->all() ?? null;
        $operationList['interactiveBox'] = $extends->get(Extend::TYPE_INTERACTIVE_BOX)?->all() ?? null;

        return $operationList;
    }
}
