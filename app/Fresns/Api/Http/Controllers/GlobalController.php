<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\GlobalConfigsDTO;
use App\Fresns\Api\Http\DTO\GlobalRolesDTO;
use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\FileHelper;
use App\Helpers\PluginHelper;
use App\Models\CommentLog;
use App\Models\PostLog;
use App\Helpers\PrimaryHelper;
use App\Models\Config;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\Notify;
use App\Exceptions\ApiException;
use App\Models\Role;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function configs(Request $request)
    {
        $dtoRequest = new GlobalConfigsDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $itemKey = array_filter(explode(',', $dtoRequest->keys));
        $itemTag = array_filter(explode(',', $dtoRequest->tags));

        $configQuery = Config::where('is_api', 1);

        if (! empty($itemKey) && ! empty($itemTag)) {
            $configQuery = Config::where('is_api', 1)->whereIn('item_key', $itemKey)->orWhereIn('item_tag', $itemTag);
        } elseif (! empty($itemKey) && empty($itemTag)) {
            $configQuery = Config::where('is_api', 1)->whereIn('item_key', $itemKey);
        } elseif (empty($itemKey) && ! empty($itemTag)) {
            $configQuery = Config::where('is_api', 1)->whereIn('item_tag', $itemTag);
        }

        $configs = $configQuery->paginate($request->get('pageSize', 50));

        $item = null;
        foreach ($configs as $config) {
            if ($config->is_multilingual == 1) {
                $item[$config->item_key] = LanguageHelper::fresnsLanguageByTableKey($config->item_key, $config->item_type, $headers['langTag']);
            } elseif ($config->item_type == 'file' && is_numeric($config->item_value)) {
                $item[$config->item_key] = ConfigHelper::fresnsConfigFileUrlByItemKey($config->item_value);
            } elseif ($config->item_type == 'plugin') {
                $item[$config->item_key] = PluginHelper::fresnsPluginUrlByUnikey($config->item_value);
            } elseif ($config->item_type == 'plugins') {
                if ($config->item_value) {
                    foreach ($config->item_value as $plugin) {
                        $item['code'] = $plugin['code'];
                        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($plugin['unikey']);
                        $itemArr[] = $item;
                    }
                    $item[$config->item_key] = $itemArr;
                }
            } else {
                $item[$config->item_key] = $config->item_value;
            }
        }

        return $this->fresnsPaginate($item, $configs->total(), $configs->perPage());
    }

    public function overview()
    {
        $headers = AppHelper::getApiHeaders();
        $userId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);

        if (empty($userId)) {
            throw new ApiException(31602);
        }

        $dialogACount = Dialog::where('a_user_id', $userId)->where('a_is_read', 0)->where('a_is_display', 1)->count();
        $dialogBCount = Dialog::where('b_user_id', $userId)->where('b_is_read', 0)->where('b_is_display', 1)->count();
        $dialogMessageCount = DialogMessage::where('recv_user_id', $userId)->where('recv_read_at', null)->where('recv_deleted_at', null)->where('is_enable', 1)->count();
        $dialogUnread['dialog'] = $dialogACount + $dialogBCount;
        $dialogUnread['message'] = $dialogMessageCount;
        $data['dialogUnread'] = $dialogUnread;

        $notify = Notify::where('user_id', $userId)->where('is_read', 0);
        $notifyUnread['system'] = $notify->where('action_type', 1)->count();
        $notifyUnread['follow'] = $notify->where('action_type', 2)->count();
        $notifyUnread['like'] = $notify->where('action_type', 3)->count();
        $notifyUnread['comment'] = $notify->where('action_type', 4)->count();
        $notifyUnread['mention'] = $notify->where('action_type', 5)->count();
        $notifyUnread['recommend'] = $notify->where('action_type', 6)->count();
        $data['notifyUnread'] = $notifyUnread;

        $draftCount['posts'] = PostLog::where('user_id', $userId)->whereIn('state', [1, 4])->count();
        $draftCount['comments'] = CommentLog::where('user_id', $userId)->whereIn('state', [1, 4])->count();
        $data['draftCount'] = $draftCount;

        return $this->success($data);
    }

    public function roles(Request $request)
    {
        $dtoRequest = new GlobalRolesDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $roles = Role::where('is_enable', 1)->where('type', $dtoRequest->type)->paginate($request->get('pageSize', 50));

        $roleList = null;
        foreach ($roles as $role) {
            foreach ($role->permission as $perm) {
                $permission[$perm['permKey']] = $perm['permValue'];
            }

            $item['rid'] = $role->id;
            $item['nicknameColor'] = $role->nickname_color;
            $item['name'] = LanguageHelper::fresnsLanguageByTableId('roles', 'name', $role->id, $headers['langTag']);
            $item['nameDisplay'] = (bool) $role->is_display_name;
            $item['icon'] = FileHelper::fresnsFileImageUrlByColumn($role->icon_file_id, $role->icon_file_url);
            $item['iconDisplay'] = (bool) $role->is_display_icon;
            $item['permission'] = $permission;
            $item['status'] = (bool) $role->is_enable;
            $roleList[] = $item;
        }

        return $this->fresnsPaginate($roleList, $roles->total(), $roles->perPage());
    }
}
