<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */
namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use Illuminate\Http\Request;
use App\Helpers\ConfigHelper;
use App\Utilities\ConfigUtility;

class PluginFunctionController extends Controller
{
    public function show(Request $request)
    {
        $theme = $request->theme;
        if (!$theme) {
            abort(404);
        }

        $themePlugin = Plugin::where('type', 4)->where('unikey', $theme)->first();

        if (!$themePlugin) {
            abort(404);
        }

        $view = $theme.'.functions';
        if (!view()->exists($view)){
            abort(404);
        }

        return view($view);
    }

    public function update(Request $request)
    {
        $theme = $request->theme;
        if (!$theme) {
            abort(404);
        }

        $themePlugin = Plugin::where('type', 4)->where('unikey', $theme)->first();

        if (!$themePlugin) {
            abort(404);
        }

        $themeJsonFile = resource_path('themes/'.$theme.'/theme.json');

        if(!\File::exists($themeJsonFile)) {
            return back()->with('failure', '配置文件未找到');
        }

        $themeConfig = json_decode(\File::get($themeJsonFile), true);
        $functionKeys = $themeConfig['functionKeys'] ?? [];

        $fresnsConfigItems = [];
        foreach($functionKeys as $functionKey) {
            $fresnsConfigItems[] = [
                'item_value' => $request->{$functionKey['itemKey']},
                'item_key' => $functionKey['itemKey'],
                'item_type' => $functionKey['itemType'],
                'item_tag' => $functionKey['itemTag'],
                'is_multilingual' => $functionKey['isMultilingual'],
                'is_enable' => $request->is_enable ? $request->is_enable[$functionKey['itemKey']] ?? 0 : 0,
                'is_custom' => 1,
            ];
        }
        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        return $this->createSuccess();
    }
}
