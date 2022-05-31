<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\GlobalConfigsDTO;
use App\Fresns\Api\Http\DTO\GlobalBlockWordsDTO;
use App\Fresns\Api\Http\DTO\GlobalRolesDTO;
use App\Fresns\Api\Http\DTO\GlobalUploadTokenDTO;
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
use App\Models\BlockWord;
use App\Models\Role;
use App\Models\Sticker;
use App\Utilities\CollectionUtility;
use App\Utilities\ExtendUtility;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    // configs
    public function configs(Request $request)
    {
        $dtoRequest = new GlobalConfigsDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $itemKey = array_filter(explode(',', $dtoRequest->keys));
        $itemTag = array_filter(explode(',', $dtoRequest->tags));

        $configQuery = Config::where('is_api', 1);

        if (!empty($itemKey) && !empty($itemTag)) {
            $configQuery->whereIn('item_key', $itemKey)->orWhereIn('item_tag', $itemTag);
        } elseif (!empty($itemKey) && empty($itemTag)) {
            $configQuery->whereIn('item_key', $itemKey);
        } elseif (empty($itemKey) && !empty($itemTag)) {
            $configQuery->whereIn('item_tag', $itemTag);
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

    // get upload token
    public function uploadToken(Request $request)
    {
        $dtoRequest = new GlobalUploadTokenDTO($request->all());

        $fileType = match ($dtoRequest->type) {
            'image' => 1,
            'video' => 2,
            'audio' => 3,
            'document' => 4,
        };

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($fileType);

        if (! $storageConfig['storageConfigStatus']) {
            throw new ApiException(32103);
        }

        $wordBody = [
            'type' => $fileType,
            'name' => $dtoRequest->name,
            'expireTime' => $dtoRequest->expireTime,
        ];

        return \FresnsCmdWord::plugin($storageConfig['service'])->getUploadToken($wordBody);
    }

    // overview
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

    // roles
    public function roles(Request $request)
    {
        $dtoRequest = new GlobalRolesDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $status = $dtoRequest->status ?? 1;

        $roleQuery = Role::where('is_enable', $status)->orderBy('rating');

        if (!empty($dtoRequest->type)) {
            $roleQuery->where('type', $dtoRequest->type);
        }

        $roles = $roleQuery->paginate($request->get('pageSize', 20));

        $roleList = null;
        foreach ($roles as $role) {
            foreach ($role->permission as $perm) {
                $permission[$perm['permKey']] = $perm['permValue'];
            }

            $item['rid'] = $role->id;
            $item['nicknameColor'] = $role->nickname_color;
            $item['name'] = LanguageHelper::fresnsLanguageByTableId('roles', 'name', $role->id, $headers['langTag']);
            $item['nameDisplay'] = (bool) $role->is_display_name;
            $item['icon'] = FileHelper::fresnsFileUrlByTableColumn($role->icon_file_id, $role->icon_file_url);
            $item['iconDisplay'] = (bool) $role->is_display_icon;
            $item['permission'] = $permission;
            $item['status'] = (bool) $role->is_enable;
            $roleList[] = $item;
        }

        return $this->fresnsPaginate($roleList, $roles->total(), $roles->perPage());
    }

    // maps
    public function maps()
    {
        $headers = AppHelper::getApiHeaders();

        $data = ExtendUtility::getPluginExtends(9, null, null, null, $headers['langTag']);

        return $this->success($data);
    }

    // contentType
    public function contentType()
    {
        $headers = AppHelper::getApiHeaders();

        $data = ExtendUtility::getPluginExtends(4, null, null, null, $headers['langTag']);

        return $this->success($data);
    }

    // stickers
    public function stickers()
    {
        $headers = AppHelper::getApiHeaders();

        $stickers = Sticker::where('is_enable', 1)->orderBy('rating')->get();

        $stickerData = [];
        foreach ($stickers as $index => $sticker) {
            $stickerData[$index]['parentCode'] = $stickers->where('id', $sticker->parent_id)->first()?->code;
            $stickerData[$index]['name'] = LanguageHelper::fresnsLanguageByTableId('stickers', 'name', $sticker->id, $headers['langTag']);
            $stickerData[$index]['code'] = $sticker->code;
            $stickerData[$index]['codeFormat'] = '['.$sticker->code.']';
            $stickerData[$index]['url'] = FileHelper::fresnsFileUrlByTableColumn($sticker->image_file_id, $sticker->image_file_url);
        }

        $stickerTree = CollectionUtility::toTree($stickerData, 'code', 'parentCode', 'stickers');

        return $this->success($stickerTree);
    }

    // blockWords
    public function blockWords(Request $request)
    {
        $dtoRequest = new GlobalBlockWordsDTO($request->all());

        $wordQuery = BlockWord::all();

        if ($dtoRequest->type = 'content') {
            $wordQuery = BlockWord::where('content_mode', '!=', 1);
        } elseif ($dtoRequest->type = 'user') {
            $wordQuery = BlockWord::where('user_mode', '!=', 1);
        } elseif ($dtoRequest->type = 'dialog') {
            $wordQuery = BlockWord::where('dialog_mode', '!=', 1);
        }

        $words = $wordQuery->paginate($request->get('pageSize', 50));

        $wordList = null;
        foreach ($words as $word) {
            $item['word'] = $word->word;
            $item['contentMode'] = $word->content_mode;
            $item['userMode'] = $word->user_mode;
            $item['dialogMode'] = $word->dialog_mode;
            $item['replaceWord'] = $word->replace_word;
            $wordList[] = $item;
        }

        return $this->fresnsPaginate($wordList, $words->total(), $words->perPage());
    }
}
