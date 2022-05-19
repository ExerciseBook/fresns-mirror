<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */
namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Helpers\ConfigHelper;
use App\Utilities\ConfigUtility;

class PluginFunctionController extends Controller
{
    protected function getThemeConfig($theme)
    {
        if (!$theme) {
            abort(404);
        }

        $themeJsonFile = resource_path('themes/'.$theme.'/theme.json');
        if (!$themeJsonFile) {
            abort(403, '配置文件未找到');
        }

        $themeConfig = json_decode(\File::get($themeJsonFile), true);

        return $themeConfig;
    }

    public function show(Request $request)
    {
        $themeConfig = $this->getThemeConfig($request->theme);

        $functionKeys = collect($themeConfig['functionKeys'] ?? []);

        $view = $request->theme.'.functions';
        if (!view()->exists($view)){
            abort(404);
        }

        $configs = Config::whereIn('item_key', $functionKeys->pluck('itemKey'))->get();
        $configValue = $configs->pluck('item_value', 'item_key');
        $configEnable = $configs->pluck('is_enable', 'item_key');
        $functionParams = [];

        // language keys
        $langKeys = $functionKeys->where('isMultilingual', true)->pluck('itemKey');
        $languages = Language::ofConfig()->whereIn('table_key', $langKeys)->get();

        foreach($functionKeys as $functionKey) {
            $key = $functionKey['itemKey'];
            $functionKey['value'] = $configValue[$key] ?? null;
            $functionKey['is_enable'] = $configEnable[$key] ?? 0;
            // 如果是文件
            if ($functionKey['itemType'] == 'file') {

                $functionKey['fileType'] = ConfigHelper::fresnsConfigFileValueTypeByItemKey($key);
                if ($functionKey['fileType'] == 'ID') {
                    $functionKey['fileUrl'] = ConfigHelper::fresnsConfigFileUrlByItemKey($key);
                } else {
                    $functionKey['fileUrl'] = $functionKey['value'];
                }
            }

            // 多语言
            if ($functionKey['isMultilingual']) {
                $functionKey['languages'] = $languages->where('table_key', $key)->values();
                $functionKey['defaultLanguage'] = $languages->where('table_key', $key)->where('lang_tag', $this->defaultLanguage)->first()['lang_content'] ?? '';
            }

            $functionKey['isEnable'] = $configEnable[$key] ?? 0;
            $functionParams[$key] = $functionKey;
        }

        $plugins = Plugin::all();



        return view($view, compact('functionParams', 'plugins'));
    }

    public function update(Request $request)
    {
        $themeConfig = $this->getThemeConfig($request->theme);

        $functionKeys = $themeConfig['functionKeys'] ?? [];

        $fresnsConfigItems = [];
        foreach($functionKeys as $functionKey) {
            if ($functionKey['itemType'] == 'file') {

                if ($request->file($functionKey['itemKey'].'_file')) {
                    $wordBody = [
                        'platform' => 4,
                        'type' => 1,
                        'tableType' => 2,
                        'tableName' => 'configs',
                        'tableColumn' => 'item_value',
                        'tableKey' => $funcitonKey['itemKey'],
                        'file' => $request->file($functionKey['itemKey'].'_file'),
                    ];
                    $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFile($wordBody);
                    if ($fresnsResp->isErrorResponse()) {
                        return back()->with('failure', $fresnsResp->getMessage());
                    }
                    $fileId = PrimaryHelper::fresnsFileIdByFid($fresnsResp->getData('fid'));
                    $request->request->set($functionKey['itemKey'], $fileId);
                } elseif ($request->get($functionKey['itemKey'].'_url')) {
                    $request->request->set($functionKey['itemKey'], $request->get($functionKey['itemKey'].'_url'));
                }
            }

            $value = $request->{$functionKey['itemKey']};
            if ($functionKey['itemType'] == 'plugins') {
                $value = array_values($value);
            }

            $fresnsConfigItems[] = [
                'item_value' => $value,
                'item_key' => $functionKey['itemKey'],
                'item_type' => $functionKey['itemType'],
                'item_tag' => $functionKey['itemTag'],
                'is_multilingual' => $functionKey['isMultilingual'],
                'is_enable' => $request->is_enable ? ($request->is_enable[$functionKey['itemKey']] ?? 0) : 0,
                'is_custom' => 1,
            ];
        }
        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        return $this->createSuccess();
    }

    public function updateLanguage(Request $request)
    {
        $key = $request->key;
        $theme = $request->theme;
        if (!$key || !$theme){
            abort(404);
        }
        $themeConfig = $this->getThemeConfig($theme);

        $functionKeys = $themeConfig['functionKeys'] ?? [];
        $functionKey = collect($functionKeys)->where('itemKey', $key)->first();
        if (!$functionKey) {
            abort(404);
        }

        foreach ($request->languages as $langTag => $content) {
            $language = Language::ofConfig()
                ->where('table_key', $key)
                ->where('lang_tag', $langTag)
                ->first();
            if (! $language) {
                // create but no content
                if (! $content) {
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'configs',
                    'table_column' => 'item_value',
                    'table_key' => $key,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }

        $content = $request->languages[$this->defaultLanguage] ?? current(array_filter($request->languages));

        $fresnsConfigItems[] = [
            'item_value' => $content,
            'item_key' => $functionKey['itemKey'],
            'item_type' => $functionKey['itemType'],
            'item_tag' => $functionKey['itemTag'],
            'is_multilingual' => $functionKey['isMultilingual'],
            'is_enable' => $request->is_enable ? ($request->is_enable[$functionKey['itemKey']] ?? 0) : 0,
            'is_custom' => 1,
        ];

        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        return $this->createSuccess();
    }
}
