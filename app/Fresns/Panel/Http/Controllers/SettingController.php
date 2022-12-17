<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use App\Fresns\Panel\Http\Requests\UpdateConfigRequest;
use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Models\Plugin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'build_type',
            'system_url',
            'panel_path',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginUpgradeCount = Plugin::where('is_upgrade', 1)->count();

        return view('FsView::dashboard.settings', compact('params', 'pluginUpgradeCount'));
    }

    public function update(UpdateConfigRequest $request)
    {
        if ($request->path && Str::startsWith($request->path, config('FsConfig.route_blacklist'))) {
            return back()->with('failure', __('FsLang::tips.secure_entry_route_conflicts'));
        }

        if ($request->build_type) {
            $buildConfig = Config::where('item_key', 'build_type')->firstOrNew();
            $buildConfig->item_value = $request->build_type;
            $buildConfig->save();
        }

        if ($request->systemUrl) {
            $url = Str::of($request->systemUrl)->trim();
            $url = Str::of($url)->rtrim('/');

            $systemUrl = Config::where('item_key', 'system_url')->firstOrNew();
            $systemUrl->item_value = $url;
            $systemUrl->save();
        }

        if ($request->panelPath) {
            $path = Str::of($request->panelPath)->trim();
            $path = Str::of($path)->rtrim('/');

            $pathConfig = Config::where('item_key', 'panel_path')->firstOrNew();
            $pathConfig->item_value = $path;
            $pathConfig->save();
        }

        return $this->updateSuccess();
    }

    // caches page
    public function caches()
    {
        $pluginUpgradeCount = Plugin::where('is_upgrade', 1)->count();

        return view('FsView::dashboard.caches', compact('pluginUpgradeCount'));
    }

    // cacheAllClear
    public function cacheAllClear()
    {
        CacheHelper::clearAllCache();

        return $this->updateSuccess();
    }

    // cacheSelectClear
    public function cacheSelectClear(Request $request)
    {
        switch ($request->type) {
            case 'config':
            break;

            case 'model':
            break;

            case 'interaction':
            break;

            case 'manage':
            break;

            case 'apiData':
            break;

            default:
                return back()->with('failure', __('FsLang::tips.requestFailure'));
            break;
        }

        return $this->requestSuccess();
    }
}
