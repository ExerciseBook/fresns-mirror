<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\Language;
use App\Models\PluginUsage;
use Illuminate\Http\Request;

class MapConfigController extends Controller
{
    public function index()
    {
        $config = Config::where('item_key', 'maps')->first();
        $mapServices = $config->item_value ?? [];
        $mapServices = collect($mapServices)->mapWithKeys(function($service) {
            return [$service['id'] => $service];
        });

        $mapKeys = collect($mapServices)->pluck('id')->map(function($id) {
            return 'map_'.$id;
        });

        $mapConfigs = Config::whereIn('item_key', $mapKeys)->get();
        $mapConfigs = $mapConfigs->mapWithKeys(function($config) {
            return [$config->item_key => $config->item_value];
        });

        $pluginUsages = PluginUsage::where('type', 9)
            ->with('plugin')
            ->get();

        $plugins = Plugin::all();

        $languages = Language::tableName('plugin_usages')
            ->where('table_field', 'name')
            ->whereIn('table_id', $pluginUsages->pluck('id'))
            ->get();

        return view('panel::system.map', compact(
            'pluginUsages', 'mapServices', 'mapConfigs',
            'plugins', 'languages',
        ));
    }

    public function store(Request $request)
    {
        $mapConfig = PluginUsage::where('parameter', $request->parameter)
            ->where('type', 9)
            ->first();
        if ($mapConfig) {
            return back()->with('failure', __('panel::panel.mapExists'));
        }

        $mapConfig = new PluginUsage;
        $mapConfig->plugin_unikey = $request->plugin_unikey;
        $mapConfig->is_enable = $request->is_enable;
		$mapConfig->icon_file_url = $request->icon_file_url;
        $mapConfig->rank_num = $request->rank_num;
        $mapConfig->parameter = $request->parameter;
        $mapConfig->type = 9;
        $mapConfig->name = $request->languages[$this->defaultLanguage] ?? (current(array_filter($request->languages)) ?: '');
        $mapConfig->save();

        $config = Config::where('item_key', 'map_'.$request->parameter)
            ->where('item_tag', 'maps')
            ->first();

        if (!$config) {
            $config = new Config();
            $config->item_key = 'map_'.$request->parameter;
            $config->item_tag = 'maps';
            $config->item_type = 'object';
            $config->is_enable = 1;
        }

        $config->item_value = [
            'mapId' => $request->parameter,
            'appId' => $request->app_id,
            'appKey' => $request->app_key,
        ];
        $config->save();

        foreach($request->languages as $langTag => $content) {
            $language = Language::tableName('plugin_usages')
                ->where('table_name', 'plugin_usages')
                ->where('table_field', 'name')
                ->where('table_id', $mapConfig->id)
                ->where('lang_tag', $langTag)
                ->first();
            if (!$language) {
                // create but no content
                if (!$content){
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'plugin_usages',
                    'table_field' => 'name',
                    'table_id' => $mapConfig->id,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }

        return $this->createSuccess();
    }

    public function update(Request $request, PluginUsage $mapConfig)
    {
        $mapConfig->plugin_unikey = $request->plugin_unikey;
        $mapConfig->is_enable = $request->is_enable;
        $mapConfig->rank_num = $request->rank_num;
        $mapConfig->parameter = $request->parameter;
		$mapConfig->icon_file_url = $request->icon_file_url;
        $mapConfig->type = 9;
        $mapConfig->name = $request->languages[$this->defaultLanguage] ?? (current(array_filter($request->languages)) ?: '');
        $mapConfig->save();

        $config = Config::where('item_key', 'map_'.$request->parameter)
            ->where('item_tag', 'maps')
            ->first();

        if (!$config) {
            $config = new Config();
            $config->item_key = 'map_'.$request->parameter;
            $config->item_tag = 'maps';
            $config->item_type = 'object';
            $config->is_enable = 1;
        }

        $config->item_value = [
            'mapId' => $request->parameter,
            'appId' => $request->app_id,
            'appKey' => $request->app_key,
        ];
        $config->save();

        foreach($request->languages as $langTag => $content) {

            $language = Language::tableName('plugin_usages')
                ->where('table_field', 'name')
                ->where('table_id', $mapConfig->id)
                ->where('lang_tag', $langTag)
                ->first();
            if (!$language) {
                // create but no content
                if (!$content){
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'plugin_usages',
                    'table_field' => 'name',
                    'table_id' => $mapConfig->id,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }

        return $this->updateSuccess();
    }
}
