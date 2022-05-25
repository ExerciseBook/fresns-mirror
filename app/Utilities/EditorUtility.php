<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\AppHelper;
use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Models\Extend;
use App\Models\Role;
use App\Models\User;

class EditorUtility
{
    // extend handle
    public static function extendHandle(array $extends)
    {
        $extendArr = Extend::whereIn('eid', array_column($extends, 'eid'))->where('is_enable', 1)->get();

        $extendList = null;
        foreach ($extendArr as $extend) {
            $item['eid'] = $extend->eid;
            $item['canDelete'] = true;
            $item['rating'] = 9;
            $item['frameType'] = $extend->frame_type;
            $item['framePosition'] = $extend->frame_position;
            $item['textContent'] = $extend->text_content;
            $item['textIsMarkdown'] = $extend->text_is_markdown;
            $item['cover'] = FileHelper::fresnsFileImageUrlByColumn($extend['cover_file_id'], $extend['cover_file_url']);
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

    // read allow handle
    public static function readAllowHandle(array $readAllowConfig)
    {
        $headers = AppHelper::getApiHeaders();

        $permission['users'] = null;
        if (empty($readAllowConfig['permission']['users'])) {
            $users = User::whereIn('uid', $readAllowConfig['permission']['users'])->first();
            foreach ($users as $user) {
                $userList = $user->getUserProfile($headers['langTag'], $headers['timezone']);
            }
            $permission['users'] = $userList;
        }

        $permission['roles'] = null;
        if (empty($readAllowConfig['permission']['roles'])) {
            $roles = Role::whereIn('id', $readAllowConfig['permission']['roles'])->first();
            foreach ($roles as $role) {
                $roleItem['rid'] = $role->id;
                $roleItem['nicknameColor'] = $role->nickname_color;
                $roleItem['name'] = LanguageHelper::fresnsLanguageByTableId('roles', 'name', $role->id, $headers['langTag']);
                $roleItem['nameDisplay'] = (bool) $role->is_display_name;
                $roleItem['icon'] = FileHelper::fresnsFileImageUrlByColumn($role->icon_file_id, $role->icon_file_url);
                $roleItem['iconDisplay'] = (bool) $role->is_display_icon;
                $roleItem['status'] = (bool) $role->is_enable;
                $roleList[] = $roleItem;
            }
            $permission['roles'] = $roleList;
        }

        $item['isAllow'] = (bool) $readAllowConfig['isAllow'];
        $item['proportion'] = $readAllowConfig['proportion'];
        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($readAllowConfig['pluginUnikey']);
        $item['btnName'] = collect($readAllowConfig['btnName'])->where('langTag', $headers['langTag'])->first()['name'] ?? null;
        $item['permission'] = $permission;

        return $item;
    }

    // user list handle
    public static function userListHandle(array $userListConfig)
    {
        $headers = AppHelper::getApiHeaders();

        $item['isUserList'] = (bool) $userListConfig['isUserList'];
        $item['userListName'] = collect($userListConfig['userListName'])->where('langTag', $headers['langTag'])->first()['name'] ?? null;
        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($userListConfig['pluginUnikey']);

        return $item;
    }

    // comment btn handle
    public static function commentBtnHandle(array $commentBtnConfig)
    {
        $headers = AppHelper::getApiHeaders();

        $item['isCommentBtn'] = (bool) $commentBtnConfig['isCommentBtn'];
        $item['btnName'] = collect($commentBtnConfig['btnName'])->where('langTag', $headers['langTag'])->first()['name'] ?? null;
        $item['btnStyle'] = $commentBtnConfig['btnStyle'];
        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($commentBtnConfig['pluginUnikey']);

        return $item;
    }
}
