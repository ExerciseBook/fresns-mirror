<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Models\CommentLog;
use App\Models\PostLog;
use App\Helpers\PrimaryHelper;
use App\Models\Config;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function configs(Request $request)
    {
        $headers = AppHelper::getApiHeaders();

        $configs = Config::paginate($request->get('pageSize', 1));

        $configList = [];
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
            $configList[] = $item;
        }

        return $this->fresnsPaginate($configList, $configs->total(), $configs->perPage());
    }

    public function overview()
    {
        $headers = AppHelper::getApiHeaders();
        $userId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);

        $dialogUnread['dialog'] = 0;
        $dialogUnread['message'] = 0;
        $data['dialogUnread'] = $dialogUnread;

        $notifyUnread['system'] = 0;
        $notifyUnread['follow'] = 0;
        $notifyUnread['like'] = 0;
        $notifyUnread['comment'] = 0;
        $notifyUnread['mention'] = 0;
        $notifyUnread['recommend'] = 0;
        $data['notifyUnread'] = $notifyUnread;

        $draftCount['posts'] = PostLog::where('user_id', $userId)->whereIn('state', [1, 4])->count();
        $draftCount['comments'] = CommentLog::where('user_id', $userId)->whereIn('state', [1, 4])->count();
        $data['draftCount'] = $draftCount;

        return $this->success($data);
    }
}
