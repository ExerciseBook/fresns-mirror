<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        // config keys
        $configKeys = [
            'engine_service',
            'engine_api_type',
            'engine_key_id',
            'engine_api_host',
            'engine_api_app_id',
            'engine_api_app_secret',
            'website_stat_code',
            'website_stat_position',
            'website_status',
            'website_number',
            'website_proportion',
            'site_china_mode',
            'china_icp_beian',
            'china_icp_license',
            'china_gongan_beian',
            'china_broadcasting_license',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginScenes = [
            'engine',
        ];
        $plugins = Plugin::all();
        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        $keyData = SessionKey::where('type', 1)->whereIn('platform_id', [2, 3, 4])->isEnable()->get();
        $keys = [];
        foreach ($keyData as $key) {
            $item['id'] = $key->id;
            $item['name'] = $key->name;
            $item['appId'] = $key->app_id;
        }

        $engine = Plugin::where('unikey', $params['engine_service'])->first();

        return view('FsView::clients.website', compact('pluginParams', 'keys', 'params', 'engine'));
    }

    public function update(Request $request)
    {
        // config keys
        $configKeys = [
            'engine_service',
            'engine_api_type',
            'engine_key_id',
            'engine_api_host',
            'engine_api_app_id',
            'engine_api_app_secret',
            'website_stat_code',
            'website_stat_position',
            'website_status',
            'website_number',
            'website_proportion',
            'site_china_mode',
            'china_icp_beian',
            'china_icp_license',
            'china_gongan_beian',
            'china_broadcasting_license',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (! $config) {
            }

            if (! $request->has($configKey)) {
                $config->setDefaultValue();
                $config->save();
                continue;
            }

            $config->item_value = $request->$configKey;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
